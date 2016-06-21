<?php

namespace Fenos\Notifynder\Collections;

use Fenos\Notifynder\Contracts\ConfigContract;
use Fenos\Notifynder\Models\Notification;
use Illuminate\Support\Arr;

class Config implements ConfigContract
{
    protected $items;

    public function __construct()
    {
        $this->items = app('config')->get('notifynder');
    }

    public function isPolymorphic()
    {
        return (bool) $this->get('polymorphic');
    }

    public function isStrict()
    {
        return (bool) $this->get('strict_extra');
    }

    public function isTranslated()
    {
        return (bool) $this->get('translation.enabled');
    }

    public function getNotificationModel()
    {
        $class = $this->get('notification_model');
        if (class_exists($class)) {
            return $class;
        }

        return Notification::class;
    }

    public function getNotifiedModel()
    {
        $class = $this->get('model');
        if (class_exists($class)) {
            return $class;
        }
        throw new \InvalidArgumentException("The model class [{$class}] doesn't exist.");
    }

    public function getAdditionalFields()
    {
        return Arr::flatten($this->get('additional_fields', []));
    }

    public function getAdditionalRequiredFields()
    {
        return Arr::flatten($this->get('additional_fields.required', []));
    }

    public function getTranslationDomain()
    {
        return $this->get('translation.domain', 'notifynder');
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    public function has($key)
    {
        return Arr::has($this->items, $key);
    }

    public function set($key, $value = null)
    {
        Arr::set($this->items, $key, $value);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
}
