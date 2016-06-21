<?php

if (! function_exists('notifynder_config')) {
    /**
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed|\Fenos\Notifynder\Contracts\ConfigContract
     */
    function notifynder_config($key = null, $default = null)
    {
        $config = app('notifynder.config');
        if (is_null($key)) {
            return $config;
        }

        return $config->get($key, $default);
    }
}

if (! function_exists('notifynder_mixed_get')) {
    function notifynder_mixed_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return '';
        }
        foreach (explode('.', $key) as $segment) {
            if (is_object($object) && isset($object->{$segment})) {
                $object = $object->{$segment};
            } elseif (is_object($object) && method_exists($object, '__get') && !is_null($object->__get($segment))) {
                $object = $object->__get($segment);
            } elseif (is_object($object) && method_exists($object, 'getAttribute') && !is_null($object->getAttribute($segment))) {
                $object = $object->getAttribute($segment);
            } elseif (is_array($object) && array_key_exists($segment, $object)) {
                $object = array_get($object, $segment, $default);
            } else {
                return value($default);
            }
        }
        return $object;
    }
}