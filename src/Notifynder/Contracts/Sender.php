<?php namespace Fenos\Notifynder\Contracts;

/**
 * Class SendSingle
 *
 * This contracts is needed to each sender classes.
 * It make sure they'll have all send method to send
 * notifications
 *
 * @package Fenos\Notifynder\Senders
 */
interface Sender
{

    /**
     * Send notification
     *
     * @param  StoreNotification $storeNotification
     * @return mixed
     */
    public function send(StoreNotification $storeNotification);
}
