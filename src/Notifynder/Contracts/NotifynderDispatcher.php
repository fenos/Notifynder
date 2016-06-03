<?php

namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\Notifynder;

/**
 * Interface NotifynderDispatcher.
 */
interface NotifynderDispatcher
{
    /**
     * It fire the event associated to the passed key,
     * trigger the listener method bound with.
     *
     * @param  Notifynder $notifynder
     * @param  string            $eventName
     * @param  string            $categoryName
     * @param  mixed|null        $values
     * @return mixed|null
     */
    public function fire(Notifynder $notifynder, $eventName, $categoryName = null, $values = []);

    /**
     * Delegate events to categories.
     *
     * @param  Notifynder $notifynder
     * @param  array             $data
     * @param  array             $events
     * @return mixed
     */
    public function delegate(Notifynder $notifynder, $data, array $events);

    /**
     * Boot The listeners.
     *
     * @param array $listeners
     */
    public function boot(array $listeners);

    /**
     * Tell the dispatcher to send
     * the notification with a custom
     * (extended method).
     *
     * @param $customMethod
     * @return $this
     */
    public function sendWith($customMethod);

    /**
     * Check if the fired method has some notifications
     * to send.
     *
     * @param $notificationsResult
     * @return bool
     */
    public function hasNotificationToSend($notificationsResult);
}
