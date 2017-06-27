<?php

namespace Fenos\Notifynder\Builder;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Fenos\Notifynder\Parsers\NotificationParser;
use Fenos\Notifynder\Models\Notification as ModelNotification;

/**
 * Class Notification.
 */
class Notification implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $requiredFields = [
        'from_id',
        'to_id',
        'category_id',
    ];

    /**
     * Notification constructor.
     */
    public function __construct()
    {
        $customRequired = notifynder_config()->getAdditionalRequiredFields();
        $this->requiredFields = array_merge($this->requiredFields, $customRequired);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function attribute($key, $default = null)
    {
        return $this->get($key, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->requiredFields as $field) {
            if (! $this->has($field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->attributes());
    }

    /**
     * @return array
     */
    public function toDbArray()
    {
        $notification = $this->toArray();
        if (array_key_exists('extra', $notification) && is_array($notification['extra'])) {
            $notification['extra'] = json_encode($notification['extra']);
        }

        return $notification;
    }

    /**
     * @return string
     */
    public function getText()
    {
        if ($this->isValid()) {
            $notification = new ModelNotification($this);
            $notifynderParse = new NotificationParser();

            return $notifynderParse->parse($notification);
        }
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->toJson();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        Arr::forget($this->attributes, $offset);
    }
}
