<?php namespace Fenos\Notifynder\Parsers;

/**
 *
 * Class parse used on collection. It permit to decode the special
 * values inserted on the notification
 *
 * @package Fenos\Notifynder\Parse
 */
class NotifynderParser
{

    /**
     * Regex to get values between curly brachet {$value}
     */
    const RULE = '/\{(.+?)(?:\{(.+)\})?\}/';

    /**
     * Parse special value from a noficiation
     * Model or a collection
     *
     * @param $item
     * @return mixed
     */
    public function parse($item)
    {
        $body = $item['body']['text'];
        $extra = $item['extra'];

        $valuesToParse = $this->getValues($body);

        if ($valuesToParse > 0) {
            return $this->replaceSpecialValues($valuesToParse, $item, $body, $extra);
        }

        return $body;
    }

    /**
     * Replace specialValues
     *
     * @param $valuesToParse
     * @param $item
     * @param $body
     * @param $extra
     * @return mixed
     */
    public function replaceSpecialValues($valuesToParse, $item, $body, $extra)
    {
        foreach ($valuesToParse as $value) {
            // get an array of nested values, means that there is a relations
            // in progress
            $value_user = explode('.', $value);

            // get name relations
            $relation = array_shift($value_user);

            if (strpos($value, $relation.'.') !== false) {
                // yes

                $body = $this->insertValuesRelation($value_user, $relation, $body, $item);
            }

            if (! is_null($extra)) {
                $body = $this->replaceExtraParameter($value, $body, $extra);
            }
        }

        return $body;
    }

    /**
     * Replace relations values
     *
     * @param $value_user
     * @param $relation
     * @param $body
     * @param $item
     * @return mixed
     */
    protected function insertValuesRelation($value_user, $relation, $body, $item)
    {
        foreach ($value_user as $value) {
            $body = preg_replace(
                "{{".$relation.".".$value."}}",
                $item[$relation][$value],
                $body
            );
        }

        return $body;
    }

    /**
     * Replace the Extra Parameter
     *
     * @param $value
     * @param $body
     * @return mixed
     */
    protected function replaceExtraParameter($value, $body, $extra)
    {
        return $item['body']['text'] = preg_replace(
            "{{".$value."}}",
            $extra,
            $body
        );
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
