<?php

namespace Fenos\Notifynder\Parsers;

use Fenos\Notifynder\Models\NotificationCategory;
use Fenos\Notifynder\Exceptions\ExtraParamsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Fenos\Notifynder\Models\Notification as ModelNotification;

/**
 * Class NotificationParser.
 */
class NotificationParser
{
    /**
     * Regex-search-rule.
     */
    const RULE = '/\{([a-zA-Z0-9_\.]+)\}/m';

    /**
     * Parse a notification and return the body text.
     *
     * @param ModelNotification $notification
     * @return string
     * @throws ExtraParamsException
     */
    public function parse($notification)
    {
        $category = $notification->category;
        if (is_null($category)) {
            throw (new ModelNotFoundException)->setModel(
                NotificationCategory::class, $notification->category_id
            );
        }
        $text = $category->template_body;

        $specialValues = $this->getValues($text);
        if (count($specialValues) > 0) {
            $specialValues = array_filter($specialValues, function ($value) use ($notification) {
                return ((is_array($notification) && isset($notification[$value])) || (is_object($notification) && isset($notification->$value))) || starts_with($value, ['extra.', 'to.', 'from.']);
            });

            foreach ($specialValues as $replacer) {
                $replace = $this->mixedGet($notification, $replacer);
                if (empty($replace) && notifynder_config()->isStrict()) {
                    throw new ExtraParamsException("The following [$replacer] param required from your category is missing.");
                }
                $text = $this->replace($text, $replace, $replacer);
            }
        }

        return $text;
    }

    /**
     * Get an array of all placehodlers.
     *
     * @param string $body
     * @return array
     */
    protected function getValues($body)
    {
        $values = [];
        preg_match_all(self::RULE, $body, $values);

        return $values[1];
    }

    /**
     * Replace a single placeholder.
     *
     * @param string $body
     * @param string $valueMatch
     * @param string $replacer
     * @return string
     */
    protected function replace($body, $valueMatch, $replacer)
    {
        $body = str_replace('{'.$replacer.'}', $valueMatch, $body);

        return $body;
    }

    /**
     * @param array|object $object
     * @param string $key
     * @param null|mixed $default
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
                continue;
            }
            if (is_object($object) && method_exists($object, '__get') && ! is_null($object->__get($segment))) {
                $object = $object->__get($segment);
                continue;
            }
            if (is_object($object) && method_exists($object, 'getAttribute') && ! is_null($object->getAttribute($segment))) {
                $object = $object->getAttribute($segment);
                continue;
            }
            if (is_array($object) && array_key_exists($segment, $object)) {
                $object = array_get($object, $segment, $default);
                continue;
            }

            return value($default);
        }

        return $object;
    }
}
