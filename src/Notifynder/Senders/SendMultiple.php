<?php

namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Contracts\DefaultSender;
use Fenos\Notifynder\Contracts\StoreNotification;

/**
 * Class SendMultiple.
 *
 * Send multiple categories
 */
class SendMultiple implements DefaultSender
{
    /**
     * @var array
     */
    protected $infoNotifications = [];

    /**
     * @param $infoNotifications
     */
    public function __construct($infoNotifications)
    {
        $this->infoNotifications = $infoNotifications;
    }

    /**
     * Send multiple notifications.
     *
     * @param  StoreNotification $sender
     * @return mixed
     */
    public function send(StoreNotification $sender)
    {
        return $sender->storeMultiple($this->infoNotifications);
    }
}
