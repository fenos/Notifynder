<?php

namespace Fenos\Notifynder\Contracts;

interface ConfigContract
{
    public function isPolymorphic();

    public function getNotificationModel();

    public function getNotifiedModel();

    public function getAdditionalFields();

    public function getAdditionalRequiredFields();

    public function get($key, $default);

    public function has($key);

    public function set($key, $value);

    public function __get($key);

    public function __set($key, $value);
}
