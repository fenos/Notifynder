<?php
/**
 * Created by Fabrizio Fenoglio.
 */

namespace Fenos\Notifynder\Senders;

/**
 * Class SendMultiple.
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
     * Send multiple notifications.
     *
     * @param StoreNotification $storeNotification
     *
     * @return mixed
     */
    public function send(StoreNotification $storeNotification)
    {
        return $storeNotification->sendMultiple($this->infoNotifications);
    }
}
