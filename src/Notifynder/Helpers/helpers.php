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
