<?php namespace Fenos\Notifynder\Parsers;

use Fenos\Notifynder\Exceptions\ExtraParamsException;
use Fenos\Notifynder\Notifications\ExtraParams;

/**
 * Class NotifynderParser
 *
 * @package Fenos\Notifynder\Parsers
 */
class NotifynderParser
{

    /**
     * Regex to get values between curly brachet {$value}
     */
    const RULE = '/\{(.+?)(?:\{(.+)\})?\}/';

    /**
     * By default it's false
     *
     * @var bool
     */
    protected static $strictMode = false;

    /**
     * Parse the body of a notification
     * with your extras values or relation
     * values
     *
     * @param $item
     * @return array|mixed
     */
    public function parse($item)
    {
        $body = $item['body']['text'];
        $extraParam = $item['extra'];

        // Decode the data passed into an array
        //$extra = json_decode($extra);
        $specialValues = $this->getValues($body);

        if ($specialValues > 0) {

            list($extractedExtra, $relationsToReplace) = $this->categorizeSpecialValues($specialValues);

            $body = $this->replaceExtraValues($extractedExtra, $extraParam, $body);
            $body = $this->replaceValuesRelations($item, $relationsToReplace, $body);
        }

        return $body;
    }

    /**
     * Set strict mode.
     * if it's enabled then will throws exceptions
     * when extra params will not be parsed correctly
     * will be handy in development
     *
     * @param bool|true $set
     */
    public static function setStrictExtra($set = true)
    {
        static::$strictMode = $set;
    }

    /**
     * I categorize into 2 arrays
     * the relations values
     * and extras values
     *
     * @param $specialValues
     * @return array
     */
    protected function categorizeSpecialValues($specialValues)
    {
        $extrasToReplace = [];
        $relationsToReplace = [];

        foreach ($specialValues as $specialValue) {

            if (starts_with($specialValue, 'extra.')) {
                $extrasToReplace[] = $specialValue;
            } else {
                if (starts_with($specialValue, 'to.') or
                    starts_with($specialValue, 'from.')
                ) {
                    $relationsToReplace[] = $specialValue;
                }
            }
        }

        return array($extrasToReplace, $relationsToReplace);
    }

    /**
     * This method replace extra values
     * within the {extra.*} namespace.
     *
     *
     * @param $extrasToReplace
     * @param $extra
     * @param $body
     * @return array
     * @throws ExtraParamsException
     */
    protected function replaceExtraValues($extrasToReplace, $extra, $body)
    {
        // I'll try my best to have returned the
        // extra param as an array
        $extra = $this->extraToArray($extra);
            
        // wildcard
        foreach ($extrasToReplace as $replacer) {
            $valueMatch = explode('.', $replacer)[1];

            // Let's cover the scenario where the developer
            // forget to add the extra param to a category that it's
            // needed. Ex: caterogy name:"hi" text:"hello {extra.name}"
            // developer store the notification without passing the value to extra
            // into the db will be NULL. This will just remove the {extra.hello}.
            // In production it's better a "typo" mistake in the text then an Exception.
            // however we can force to throw an Exception for development porpose
            // NotifynderParser::setExtraStrict(true);
            if ( !is_array($extra) or (is_array($extra) and count($extra) == 0) ) {

                $body = $this->replaceBody($body, '', $replacer);

                // In strict mode you'll be aware
                if (static::$strictMode) {
                    $error = "the following [$replacer] param required from your category it's missing. Did you forget to store it?";
                    throw new ExtraParamsException($error);
                }

                break;
            }

            if (array_key_exists($valueMatch, $extra)) {

                $body = $this->replaceBody($body, $extra[$valueMatch], $replacer);
            }
        }

        return $body;
    }

    /**
     * Replace relations values as
     * 'to' and 'from', that means you
     * can have parsed value from the current
     * relation {to.name} name who received
     * notification
     *
     * @param $item
     * @param $relationsToReplace
     * @param $body
     * @return mixed
     */
    protected function replaceValuesRelations($item, $relationsToReplace, $body)
    {
        foreach ($relationsToReplace as $replacer) {
            $valueMatch = explode('.', $replacer);
            $relation = $valueMatch[0];
            $field = $valueMatch[1];

            $body = str_replace('{'.$replacer.'}', $item[$relation][$field], $body);
        }

        return $body;
    }

    /**
     * Get the values between {}
     * and return an array of it
     *
     * @param $body
     * @return mixed
     */
    protected function getValues($body)
    {
        $values = [];
        preg_match_all(self::RULE, $body, $values);

        return $values[1];
    }

    /**
     * Trying to transform extra in from few datatypes
     * to array type
     *
     * @param $extra
     * @return array|mixed
     */
    protected function extraToArray($extra)
    {
        if ($this->isJson($extra)) {

            $extra = json_decode($extra, true);
            return $extra;

        } else if ($extra instanceof ExtraParams) {
            $extra = $extra->toArray();
            return $extra;
        }

        return null;
    }

    /**
     * Replace body of the category
     *
     * @param $body
     * @param $replacer
     * @param $valueMatch
     * @return mixed
     */
    protected function replaceBody($body, $valueMatch, $replacer)
    {
        $body = str_replace('{'.$replacer.'}', $valueMatch, $body);

        return $body;
    }

    /**
     * Check if is a json string
     *
     * @param $value
     * @return bool
     */
    protected function isJson($value)
    {
        if ( ! is_string($value)) {
            return false;
        }

        json_decode($value);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}