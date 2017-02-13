<?php

namespace Fenos\Notifynder\Helpers;

use DateTime;
use Traversable;
use Carbon\Carbon;
use InvalidArgumentException;
use Fenos\Notifynder\Models\Notification;

/**
 * Class TypeChecker.
 */
class TypeChecker
{
    /**
     * @param $value
     * @param bool $strict
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function isString($value, $strict = true)
    {
        if (! is_string($value)) {
            if ($strict) {
                throw new InvalidArgumentException('The value passed must be a string');
            }

            return false;
        }

        return true;
    }

    /**
     * @param $value
     * @param bool $strict
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function isNumeric($value, $strict = true)
    {
        if (! is_numeric($value)) {
            if ($strict) {
                throw new InvalidArgumentException('The value passed must be a number');
            }

            return false;
        }

        return true;
    }

    /**
     * @param $value
     * @param bool $strict
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function isDate($value, $strict = true)
    {
        if ($value instanceof Carbon || $value instanceof DateTime) {
            return true;
        }

        if ($strict) {
            throw new InvalidArgumentException('The value passed must be an instance of Carbon\\Carbon or DateTime');
        }

        return false;
    }

    /**
     * @param $value
     * @param bool $strict
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function isArray($value, $strict = true)
    {
        if (self::isIterable($value, $strict) && is_array($value)) {
            return true;
        }

        if ($strict) {
            throw new InvalidArgumentException('The value passed must be an array');
        }

        return false;
    }

    /**
     * @param $value
     * @param bool $strict
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function isIterable($value, $strict = true)
    {
        if ((is_array($value) || $value instanceof Traversable) && count($value) > 0) {
            return true;
        }

        if ($strict) {
            throw new InvalidArgumentException('The value passed must be iterable');
        }

        return false;
    }

    /**
     * @param $notification
     * @param bool $strict
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function isNotification($notification, $strict = true)
    {
        if (! is_a($notification, app('notifynder.resolver.model')->getModel(Notification::class))) {
            if ($strict) {
                throw new InvalidArgumentException('The value passed must be an Notification Model instance');
            }

            return false;
        }

        return true;
    }
}
