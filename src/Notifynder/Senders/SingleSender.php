<?php

namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;

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
        $model = app('notifynder.resolver.model')->getModel(Notification::class);

        $notification = new $model($this->notification);

        return $notification->save();
    }
}
