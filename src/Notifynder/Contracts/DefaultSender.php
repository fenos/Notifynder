<?php

namespace Fenos\Notifynder\Contracts;

/**
 * Class SendSingle.
 *
 * This contracts is needed to each default sender class.
 * It make sure they'll have all send method to send
 * notifications
 */
interface DefaultSender
{
    /**
     * Send notification.
     *
     * @param  StoreNotification $sender
     * @return mixed
     */
    public function send(StoreNotification $sender);
}
