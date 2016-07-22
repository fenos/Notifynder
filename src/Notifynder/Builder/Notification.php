<?php

namespace Fenos\Notifynder\Builder;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use JsonSerializable;

class Notification implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    protected $attributes = [];

    protected $requiredFields = [
        'from_id',
        'to_id',
        'category_id',
    ];

    public function __construct()
    {
        $customRequired = notifynder_config()->getAdditionalRequiredFields();
        $this->requiredFields = array_merge($this->requiredFields, $customRequired);
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function attribute($key, $default = null)
    {
        return $this->get($key, $default);
    }

    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);
    }

    public function isValid()
    {
        foreach ($this->requiredFields as $field) {
            if (! $this->has($field)) {
                return false;
            }
        }

        return true;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->attributes());
    }

    public function toDbArray()
    {
        $notification = $this->toArray();
        if (is_array($notification['extra'])) {
            $notification['extra'] = json_encode($notification['extra']);
        }

        return $notification;
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        Arr::forget($this->attributes, $offset);
    }
}
