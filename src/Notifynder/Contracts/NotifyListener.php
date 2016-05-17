<?php

namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\Handler\NotifynderEvent;

/**
 * Interface NotifyListener.
 *
 * This interface is needed when u want dispatch
 * event through the native laravel dispatcher
 * and not from Notifynder::fire()
 */
interface NotifyListener
{
    /**
     * @return NotifynderEvent
     */
    public function getNotifynderEvent();
}
