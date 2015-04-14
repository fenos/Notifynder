<?php

use Fenos\Notifynder\Contracts\NotifynderSender;
use Fenos\Notifynder\Contracts\Sender;
use Fenos\Notifynder\Contracts\StoreNotification;

/**
 * Class CustomSender
 */
class CustomDefaultSender implements Sender
{
    /**
     * @var array
     */
    protected $notifications;

    /**
     * @var \Fenos\Notifynder\NotifynderManager
     */
    private $notifynder;

    /**
     * @param array                        $notifications
     * @param \Fenos\Notifynder\NotifynderManager $notifynder
     */
    function __construct(array $notifications,\Fenos\Notifynder\NotifynderManager $notifynder)
    {
        $this->notifications = $notifications;
        $this->notifynder = $notifynder;
    }

    /**
     * Send notification
     *
     * @param NotifynderSender $sender
     * @return mixed
     */
    public function send(NotifynderSender $sender)
    {
//        dd($storeNotification);
        return $sender->send($this->notifications);
    }
}