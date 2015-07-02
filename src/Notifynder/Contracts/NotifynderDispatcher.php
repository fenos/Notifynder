<?php namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\Notifynder;

/**
 * Interface NotifynderDispatcher
 *
 * @package Fenos\Notifynder\Contracts
 */
interface NotifynderDispatcher
{

    /**
     * It fire the event associated to the passed key,
     * trigging the listener method bound with
     *
     * @param  Notifynder $notifynder
     * @param  string            $eventName
     * @param  string            $category_name
     * @param  mixed|null        $values
     * @return mixed|null
     */
    public function fire(Notifynder $notifynder, $eventName, $category_name = null, $values = []);

    /**
     * Deletegate events to categories
     *
     * @param  Notifynder $notifynder
     * @param  array             $data
     * @param  array             $events
     * @return mixed
     */
    public function delegate(Notifynder $notifynder, $data = [], array $events);

    /**
     * Boot The listeners
     *
     * @param array $listeners
     */
    public function boot(array $listeners);

    /**
     * Tell the disptacher to send
     * the notification with a custom
     * (extended method)
     *
     * @param $customMethod
     * @return $this
     */
    public function sendWith($customMethod);

    /**
     * Check if the fired method has some notifications
     * to send
     *
     * @param $notificationsResult
     * @return bool
     */
    public function hasNotificationToSend($notificationsResult);
}
