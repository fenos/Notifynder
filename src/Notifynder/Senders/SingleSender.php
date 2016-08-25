<?php

namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Fenos\Notifynder\Models\Notification;

/**
 * Class SingleSender.
 */
class SingleSender implements SenderContract
{
    /**
     * @var \Fenos\Notifynder\Builder\Notification
     */
    protected $notification;

    /**
     * SingleSender constructor.
     *
     * @param array $notifications
     */
    public function __construct(array $notifications)
    {
        $this->notification = array_values($notifications)[0];
    }

    /**
     * Send the single notification.
     *
     * @param SenderManagerContract $sender
     * @return bool
     */
    public function send(SenderManagerContract $sender)
    {
        $notification = new Notification($this->notification);

        return $notification->save();
    }
}
