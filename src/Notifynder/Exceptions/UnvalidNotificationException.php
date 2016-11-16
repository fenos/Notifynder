<?php

namespace Fenos\Notifynder\Exceptions;

use Exception;
use Fenos\Notifynder\Builder\Notification;

/**
 * Class UnvalidNotificationException.
 */
class UnvalidNotificationException extends Exception
{
    /**
     * @var Notification
     */
    public $notification;

    /**
     * UnvalidNotificationException constructor.
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        parent::__construct('The given data failed to pass validation.');

        $this->notification = $notification;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
