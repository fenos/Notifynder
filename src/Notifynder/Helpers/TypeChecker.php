<?php

namespace Fenos\Notifynder\Helpers;

use Traversable;
use DateTime;
use Carbon\Carbon;
use InvalidArgumentException;

class TypeChecker
{
    public function isString($value)
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException('The value passed must be a string');
        }

        return true;
    }

    public function isNumeric($value)
    {
        if (! is_numeric($value)) {
            throw new InvalidArgumentException('The value passed must be a number');
        }

        return true;
    }

    public function isDate($value)
    {
        if ($value instanceof Carbon || $value instanceof DateTime) {
            return true;
        }

        throw new InvalidArgumentException('The value passed must be an instance of Carbon\\Carbon or DateTime');
    }

    public function isArray($value)
    {
        if (is_array($value) && count($value) > 0) {
            return true;
        }

        throw new InvalidArgumentException('The value passed must be an array');
    }

    public function isIterable($value)
    {
        if ((is_array($value) || $value instanceof Traversable) && count($value) > 0) {
            return true;
        }

        throw new InvalidArgumentException('The value passed must be iterable');
    }
}
