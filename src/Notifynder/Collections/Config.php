<?php

namespace Fenos\Notifynder\Collections;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Fenos\Notifynder\Contracts\ConfigContract;

/**
 * Class Config.
 */
class Config implements ConfigContract
{
    /**
     * @var array
     */
    protected $items;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->reload();
    }

    /**
     * @return bool
     */
    public function isPolymorphic()
    {
        return (bool) $this->get('polymorphic');
    }

    /**
     * @return bool
     */
    public function isStrict()
    {
        return (bool) $this->get('strict_extra');
    }

    /**
     * @return bool
     */
    public function isTranslated()
    {
        return (bool) $this->get('translation.enabled');
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function getNotifiedModel()
    {
        $class = $this->get('model');
        if (class_exists($class)) {
            return $class;
        }
        throw new InvalidArgumentException("The model class [{$class}] doesn't exist.");
    }

    /**
     * @return array
     */
    public function getAdditionalFields()
    {
        return Arr::flatten($this->get('additional_fields', []));
    }

    /**
     * @return array
     */
    public function getAdditionalRequiredFields()
    {
        return Arr::flatten($this->get('additional_fields.required', []));
    }

    /**
     * @return string
     */
    public function getTranslationDomain()
    {
        return $this->get('translation.domain', 'notifynder');
    }

    /**
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);
    }

    /**
     * @param string $key
     * @param null $value
     */
    public function set($key, $value = null)
    {
        Arr::set($this->items, $key, $value);
        app('config')->set('notifynder.'.$key, $value);
    }

    /**
     * @param string $key
     */
    public function forget($key)
    {
        Arr::forget($this->items, $key);
        app('config')->offsetUnset('notifynder.'.$key);
    }

    public function reload()
    {
        $this->items = app('config')->get('notifynder');
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
}
