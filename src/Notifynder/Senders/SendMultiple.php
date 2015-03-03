<?php namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Contracts\Sender;
use Fenos\Notifynder\Contracts\StoreNotification;

/**
 * Class SendMultiple
 *
 * Send multiple categories
 *
 * @package Fenos\Notifynder\Senders
 */
class SendMultiple implements Sender
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
     * Send multiple notifications
     *
     * @param  StoreNotification $storeNotification
     * @return mixed
     */
    public function send(StoreNotification $storeNotification)
    {
        return $storeNotification->storeMultiple($this->infoNotifications);
    }
}
