<?php
namespace Fenos\Notifynder\Exceptions;

use Exception;
use Fenos\Notifynder\Builder\Notification;

class UnvalidNotificationException extends Exception
{
    public $notification;

    public function __construct(Notification $notification)
    {
        parent::__construct('The given data failed to pass validation.');

        $this->notification = $notification;
    }

    public function getNotification()
    {
        return $this->notification;
    }
}