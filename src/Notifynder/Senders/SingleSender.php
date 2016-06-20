<?php

namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Contracts\SenderContract;
use Fenos\Notifynder\Contracts\SenderManagerContract;
use Fenos\Notifynder\Models\Notification;

class SingleSender implements SenderContract
{
    protected $notification;

    public function __construct(array $notifications)
    {
        $this->notification = array_values($notifications)[0];
    }

    public function send(SenderManagerContract $sender)
    {
        $notification = new Notification($this->notification);

        return $notification->save();
    }
}
