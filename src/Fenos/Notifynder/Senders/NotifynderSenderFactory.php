<?php

namespace Fenos\Notifynder\Senders;

use Fenos\Notifynder\Groups\NotifynderGroup;
use Fenos\Notifynder\Notifynder;

/**
 * Class NotifynderSenderFactory.
 */
class NotifynderSenderFactory
{
    /**
     * @var NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @param NotifynderGroup $notifynderGroup
     */
    public function __construct(NotifynderGroup $notifynderGroup)
    {
        $this->notifynderGroup = $notifynderGroup;
    }

    /**
     * Get the right sender when the data is
     * passed.
     *
     * @param array $infoNotifications
     * @param       $category
     *
     * @return SendMultiple|SendOne
     */
    public function getSender(array $infoNotifications, $category = null)
    {
        // if the array is multidimesional
        // it means that we want to send
        // multiple notifications
        if ($this->isMultiArray($infoNotifications)) {
            return $this->sendMultiple($infoNotifications);
        } else {
            return $this->sendSingle($infoNotifications, $category);
        }
    }

    /**
     * Send Single Notification Sender.
     *
     * @param array $infoNotifications
     * @param       $category
     *
     * @return SendOne
     */
    public function sendSingle(array $infoNotifications, $category)
    {
        return new SendOne($infoNotifications, $category);
    }

    /**
     * Send Multiple Notification Sender.
     *
     * @param array $infoNotifications
     *
     * @return SendMultiple
     */
    public function sendMultiple(array $infoNotifications)
    {
        return new SendMultiple($infoNotifications);
    }

    /**
     * Get the the send group instance.
     *
     * @param Notifynder       $notifynder
     * @param string           $group_name
     * @param array | \Closure $info
     *
     * @return SendGroup
     */
    public function sendGroup(Notifynder $notifynder, $group_name, $info)
    {
        return new SendGroup($notifynder, $this->notifynderGroup, $group_name, $info);
    }

    /**
     * Check if the array passed is
     * multidimensional.
     *
     * @param $arr
     *
     * @return bool
     */
    public function isMultiArray(array $arr)
    {
        $rv = array_filter($arr, 'is_array');
        if (count($rv) > 0) {
            return true;
        }

        return false;
    }
}
