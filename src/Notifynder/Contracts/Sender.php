<?php

namespace Fenos\Notifynder\Contracts;

/**
 * Interface Sender.
 */
interface Sender
{
    /**
     * Send a custom notification.
     *
     * @param NotifynderSender $notifynderSender
     * @return mixed
     */
    public function send(NotifynderSender $notifynderSender);
}
