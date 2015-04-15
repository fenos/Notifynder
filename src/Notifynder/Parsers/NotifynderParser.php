<?php namespace Fenos\Notifynder\Parsers;

/**
 * Class NotifynderParser
 *
 * @package Fenos\Notifynder\Parsers
 */
class NotifynderParser {

    /**
     * Regex to get values between curly brachet {$value}
     */
    const RULE = '/\{(.+?)(?:\{(.+)\})?\}/';

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
        $extra = $item['extra'];

        // Decode the data passed into an array
        $extra = json_decode($extra);
        $specialValues = $this->getValues($body);

        if ($specialValues > 0) {

            list($extrasToReplace, $relationsToReplace) = $this->categorizeSpecialValues($specialValues);

            $body = $this->replaceExtraValues($extrasToReplace, $extra, $body);
            $body = $this->replaceValuesRelations($item, $relationsToReplace, $body);
        }

        return $body;
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
     * of the given extra values specified
     * in the extra field of the notification
     * (parsed from Json) I use a convention
     * to keep all the extras values under
     * an {extra.*} namespace
     *
     * @param $extrasToReplace
     * @param $extra
     * @param $body
     * @return array
     */
    protected function replaceExtraValues($extrasToReplace, $extra, $body)
    {
        // replace the values specified in the extra
        // wildcard
        foreach ($extrasToReplace as $replacer) {
            $valueMatch = explode('.', $replacer)[1];

            if (array_key_exists($valueMatch, $extra)) {

                $body = str_replace('{'.$replacer.'}', $extra->{$valueMatch}, $body);
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
}