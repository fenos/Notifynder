<?php namespace Fenos\Notifynder\Parse;

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
    * @var \Fenos\Notifynder\Models\Notification
    */
    protected $notification;

    /**
    * @var $items
    */
    protected $items;

    /**
    * @var $loader
    */
    protected $loader = array();

    /**
     * @param $items
     */
    function __construct( $items )
    {
        $this->items = $items;
    }

    /**
     * Parse the body of the notifications
     * Replacing the default value with the
     * right parameter
     *
     * @return mixed
     */
    public function parse()
    {
        // for each items of the collection
        foreach ($this->items as $key => $notification) {

            // get specials parameters between curly brachet
            $values = $this->getValues($this->items[$key]['body']['text']);

            // there are any?
            if ( count($values) > 0 ) // yes
            {
                $this->replaceSpecialValues($values,$key);
            }
        }

        return $this->items;
    }


    /**
     * Replace Special value of the body
     * of the items I pass as secoond parameter
     * The key of the main array of the result so it can
     * merge the result properly
     *
     * @param $values
     * @param $keyItems
     * @return mixed
     */
    public function replaceSpecialValues($values,$keyItems)
    {
        // for each special values
        foreach ($values as $value)
        {
            // get an array of nested values, means that there is a relations
            // in progress
            $value_user = explode('.', $value);

            // get name relations
            $relation = array_shift($value_user);

            // check if there is any value with the name of the relation just in case
            if ( strpos($value, $relation.'.') !== false) // yes
            {
                $this->insertValues($keyItems, $value_user, $relation);
            }
            else
            {
                // no values relations
                $this->items[$keyItems]['body']['text'] = preg_replace(
                    "{{".$value."}}",
                    $this->items[$keyItems]['extra'],
                    $this->items[$keyItems]['body']['text']
                );
            }
        }

        return $this->items;
    }

    /**
     * @param $keyItems
     * @param $value_user
     * @param $relation
     * @return mixed
     */
    public function insertValues($keyItems, $value_user, $relation)
    {
        // for each values with relations
        foreach ($value_user as $value)
        {
            $keyName = $this->items[$keyItems]['body']['name'] . $relation . $value;

            if (! array_key_exists($keyName, $this->loader))
            {
                // switch the special attribute with the right value
                $this->items[$keyItems]['body']['text'] = preg_replace(
                    "{{" . $relation . "." . $value . "}}",
                    $this->items[$keyItems][$relation][$value],
                    $this->items[$keyItems]['body']['text']
                );

                // eager loading
                $this->loader[$keyName] = $this->items[$keyItems]['body']['text'];
            }

            $this->items[$keyItems]['body']['text'] = $this->loader[$keyName];
        }
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
