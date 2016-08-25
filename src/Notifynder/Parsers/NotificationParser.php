<?php

namespace Fenos\Notifynder\Parsers;

use Fenos\Notifynder\Exceptions\ExtraParamsException;
use Fenos\Notifynder\Models\Notification;

/**
 * Class NotificationParser
 * @package Fenos\Notifynder\Parsers
 */
class NotificationParser
{
    /**
     * Regex-search-rule
     */
    const RULE = '/\{([a-zA-Z0-9_\.]+)\}/m';

    /**
     * Parse a notification and return the body text.
     *
     * @param Notification $notification
     * @return string
     * @throws ExtraParamsException
     */
    public function parse(Notification $notification)
    {
        $text = $notification->template_body;

        $specialValues = $this->getValues($text);
        if (count($specialValues) > 0) {
            $specialValues = array_filter($specialValues, function ($value) use ($notification) {
                return isset($notification->$value) || starts_with($value, ['extra.', 'to.', 'from.']);
            });

            foreach ($specialValues as $replacer) {
                $replace = notifynder_mixed_get($notification, $replacer);
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
}
