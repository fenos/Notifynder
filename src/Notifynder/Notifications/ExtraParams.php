<?php namespace Fenos\Notifynder\Notifications;

use ArrayAccess;
use stdClass;

/**
 * Class Jsonable
 *
 * @package Fenos\Notifynder\Notifications
 */
class ExtraParams implements ArrayAccess
{

    /**
     * @var array|stdClass|string
     */
    protected $extraParams;

    /**
     * Jsonable constructor.
     *
     * @param $extraParams
     */
    public function __construct($extraParams)
    {
        if ($this->isJson($extraParams)) {
            $this->extraParams = json_decode($extraParams,true);
        }
        else {
            $this->extraParams = (array) $extraParams;
        }
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->extraParams);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        // Already possible json
        if ($this->isJson($this->extraParams)) {
            return json_decode($this->extraParams, true);
        }

        return $this->extraParams;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     */
    function __toString()
    {
        $extraParams = $this->extraParams;

        if (is_array($extraParams) || $extraParams instanceof stdClass) {
            return $this->toJson();
        }

        return $extraParams;
    }

    /**
     * Check if the extra param
     * exists
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        $arr = $this->extraParams;

        return isset($arr[$name]);
    }

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     * @return mixed
     */
    function __get($name)
    {
        $params = $this->toArray();

        return $params[$name];
    }


    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->extraParams[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->extraParams[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->extraParams[$offset]);
    }

    /**
     * Check if the value
     * is a json string
     *
     * @param $value
     * @return bool
     */
    public function isJson($value)
    {
        if (!is_string($value)) return false;

        json_decode($value);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}