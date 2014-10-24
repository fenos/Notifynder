<?php namespace Fenos\Notifynder\Parse;

use Illuminate\Database\Eloquent\Collection;

/**
 *
 * Class parse used on collection. It permit to decode the special
 * values inserted on the notification
 *
 * @package Fenos\Notifynder\Parse
 */
class NotifynderParse
{

    /**
     * Regex to get values between curly brachet {$value}
     */
    const RULE = '/\{(.+?)(?:\{(.+)\})?\}/';

    /**
     * @var array
     */
    protected $item;

    /**
     * @var array
     */
    protected $container_values = [];

    /**
     * @param $item
     */
    function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Parse special value from a noficiation
     * Model or a collection
     */
    public function parse()
    {
        if ($this->item instanceof Collection)
        {
            return $this->parseCollection();
        }
        else
        {
            $valuesToParse = $this->getValues($this->item['body']['text']);

            if ($valuesToParse > 0)
            {
                return $this->replaceSpecialValues($valuesToParse,$this->item);
            }
        }
    }

    /**
     * Parse special values from a collection
     */
    public function parseCollection()
    {
        $collectionItems = $this->item->getCollectionItems();

        foreach( $collectionItems as $key => $value)
        {
            $valuesToParse = $this->getValues($collectionItems[$key]['body']['text']);

            if ($valuesToParse > 0)
            {
                $this->replaceSpecialValues($valuesToParse,$value);
            }
        }

        return $collectionItems;
    }

    /**
     * Replace specialValues
     *
     * @param $valuesToParse
     * @param $item
     */
    public function replaceSpecialValues($valuesToParse,$item)
    {
        foreach($valuesToParse as $value)
        {
            // get an array of nested values, means that there is a relations
            // in progress
            $value_user = explode('.', $value);

            // get name relations
            $relation = array_shift($value_user);

            if ( strpos($value, $relation . '.') !== false ) // yes
            {
                $this->insertValuesRelation($value_user, $relation,$item);
            }
            else
            {
                $this->replaceExtraParameter($value,$item);
            }
        }

        return $item;
    }

    /**
     * Replace relations values
     *
     * @param $value_user
     * @param $relation
     * @param $item
     */
    private function insertValuesRelation($value_user, $relation, $item)
    {
        foreach($value_user as $value)
        {
            $key = $item['id'].$relation.$value;

            if ( ! array_key_exists($key,$this->container_values))
            {
                $item['body']['text'] = preg_replace(
                    "{{" . $relation . "." . $value . "}}",
                    $item[$relation][$value],
                    $item['body']['text']
                );

                $this->container_values[$key] = $item['body']['text'];
            }
            else
            {
                $item['body']['text'] = $this->container_values[$key];
            }
        }
    }

    /**
     * Replace the Extra Parameter
     *
     * @param $value
     * @param $item
     */
    public function replaceExtraParameter($value,$item)
    {
        $item['body']['text'] = preg_replace(
            "{{".$value."}}",
            $item['extra'],
            $item['body']['text']
        );
    }

    /**
     * Get the values between {}
     * and return an array of it
     *
     * @param $body
     * @return mixed
     */
    public function getValues($body)
    {
        $values = [];
        preg_match_all(self::RULE, $body, $values);
        return $values[1];
    }
}