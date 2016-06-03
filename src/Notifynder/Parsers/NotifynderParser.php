<?php

namespace Fenos\Notifynder\Parsers;

use Fenos\Notifynder\Exceptions\ExtraParamsException;
use Fenos\Notifynder\Notifications\ExtraParams;

/**
 * Class NotifynderParser.
 */
class NotifynderParser
{
    /**
     * Regex to get values between curly bracket {$value}.
     */
    const RULE = '/\{(.+?)(?:\{(.+)\})?\}/';

    /**
     * By default it's false.
     *
     * @var bool
     */
    protected static $strictMode = false;

    /**
     * Parse the body of a notification
     * with your extras values or relation
     * values.
     *
     * @param $item
     * @return string
     * @throws \Fenos\Notifynder\Exceptions\ExtraParamsException
     */
    public function parse($item)
    {
        $body = $item['body']['text'];

        $item['extra'] = $this->extraToArray($item['extra']);
        $specialValues = $this->getValues($body);

        if (count($specialValues) > 0) {
            $specialValues = array_filter($specialValues, function ($value) {
                return starts_with($value, 'extra.') || starts_with($value, 'to.') || starts_with($value, 'from.');
            });

            foreach ($specialValues as $replacer) {
                $replace = $this->mixedGet($item, $replacer);
                if (empty($replace) && static::$strictMode) {
                    $error = "the following [$replacer] param required from your category it's missing. Did you forget to store it?";
                    throw new ExtraParamsException($error);
                }
                $body = $this->replaceBody($body, $replace, $replacer);
            }
        }

        return $body;
    }

    /**
     * Set strict mode.
     * if it's enabled then will throws exceptions
     * when extra params will not be parsed correctly
     * will be handy in development.
     *
     * @param bool|true $set
     */
    public static function setStrictExtra($set = true)
    {
        static::$strictMode = $set;
    }

    /**
     * Get the values between {}
     * and return an array of it.
     *
     * @param $body
     * @return mixed
     */
    protected function getValues($body)
    {
        $values = [];
        preg_match_all(self::RULE, $body, $values);

        return $values[1];
    }

    /**
     * Trying to transform extra in from few data types
     * to array type.
     *
     * @param $extra
     * @return array|mixed
     */
    protected function extraToArray($extra)
    {
        if ($this->isJson($extra)) {
            $extra = json_decode($extra, true);

            return $extra;
        } elseif ($extra instanceof ExtraParams) {
            $extra = $extra->toArray();

            return $extra;
        }
    }

    /**
     * Replace body of the category.
     *
     * @param $body
     * @param $replacer
     * @param $valueMatch
     * @return mixed
     */
    protected function replaceBody($body, $valueMatch, $replacer)
    {
        $body = str_replace('{'.$replacer.'}', $valueMatch, $body);

        return $body;
    }

    /**
     * Check if is a json string.
     *
     * @param $value
     * @return bool
     */
    protected function isJson($value)
    {
        if (! is_string($value)) {
            return false;
        }

        json_decode($value);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Get a value by dot-key of an array, object or mix of both.
     *
     * @param array|object $object
     * @param string $key
     * @param null $default
     * @return mixed
     */
    protected function mixedGet($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return '';
        }

        foreach (explode('.', $key) as $segment) {
            if (is_object($object) && isset($object->{$segment})) {
                $object = $object->{$segment};
            } elseif (is_object($object) && method_exists($object, '__get') && ! is_null($object->__get($segment))) {
                $object = $object->__get($segment);
            } elseif (is_object($object) && method_exists($object, 'getAttribute') && ! is_null($object->getAttribute($segment))) {
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
