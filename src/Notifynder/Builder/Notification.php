<?php
namespace Fenos\Notifynder\Builder;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;

class Notification implements Arrayable, Jsonable, JsonSerializable
{
    protected $attributes = [];

    public function attributes()
    {
        return $this->attributes;
    }

    public function attribute($key, $default = null)
    {
        return $this->get($key, $default);
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);
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
        }, $this->attributes);
    }

    public function __toString()
    {
        return $this->toJson();
    }
}