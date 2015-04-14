<?php namespace Fenos\Notifynder\Contracts;

/**
 * Interface Sender
 *
 * @package Fenos\Notifynder\Contracts
 */
interface Sender {

    /**
     * Send a custom notification
     *
     * @param NotifynderSender $notifynderSender
     * @return mixed
     */
    public function send(NotifynderSender $notifynderSender);
}