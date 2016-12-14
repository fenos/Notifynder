<?php

use Fenos\Notifynder\Handler\NotifynderEvent;
use Fenos\Notifynder\Contracts\NotifyListener;

/**
 * Class NotifyEvent.
 */
class NotifyEvent implements NotifyListener
{
    /**
     * @var NotifynderEvent
     */
    public $notifynderEvent;

    /**
     * @param $notifynderEvent
     */
    public function __construct(NotifynderEvent $notifynderEvent)
    {
        $this->notifynderEvent = $notifynderEvent;
    }

    /**
     * @return NotifynderEvent
     */
    public function getNotifynderEvent()
    {
        return $this->notifynderEvent;
    }
}
