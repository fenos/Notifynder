<?php

namespace Fenos\Notifynder\Contracts;

/**
 * Interface ConfigContract.
 */
interface ConfigContract
{
    /**
     * @return bool
     */
    public function isPolymorphic();

    /**
     * @return bool
     */
    public function isStrict();

    /**
     * @return bool
     */
    public function isTranslated();

    /**
     * @return string
     */
    public function getNotifiedModel();

    /**
     * @return array
     */
    public function getAdditionalFields();

    /**
     * @return array
     */
    public function getAdditionalRequiredFields();

    /**
     * @return string
     */
    public function getTranslationDomain();

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default);

    /**
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value);
}
