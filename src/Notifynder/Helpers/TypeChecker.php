<?php

namespace Fenos\Notifynder\Helpers;

use Carbon\Carbon;
use DateTime;
use InvalidArgumentException;
use Traversable;

class TypeChecker
{
    public static function isString($value, $strict = true)
    {
        if (!is_string($value)) {
            if ($strict) {
                throw new InvalidArgumentException('The value passed must be a string');
            }
            return false;
        }

        return true;
    }

    public static function isNumeric($value, $strict = true)
    {
        if (!is_numeric($value)) {
            if ($strict) {
                throw new InvalidArgumentException('The value passed must be a number');
            }
            return false;
        }

        return true;
    }

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

    public static function isArray($value, $strict = true)
    {
        if (is_array($value) && count($value) > 0) {
            return true;
        }

        if ($strict) {
            throw new InvalidArgumentException('The value passed must be an array');
        }
        return false;
    }

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

    public static function isNotification($notification, $strict = true)
    {
        if (!is_a($notification, notifynder_config()->getNotificationModel())) {
            if ($strict) {
                throw new InvalidArgumentException('The value passed must be an Notification Model instance');
            }
            return false;
        }
        return true;
    }
}
