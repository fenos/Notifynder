<?php namespace Fenos\Notifynder\Contracts;

use Fenos\Notifynder\NotifynderManager;

interface NotifynderDispatcher
{

    /**
     * It fire the event associated to the passed key,
     * trigging the listener method bound with
     *
     * @param  NotifynderManager $notifynder
     * @param  string            $eventName
     * @param  string            $category_name
     * @param  mixed|null        $values
     * @return mixed|null
     */
    public function fire(NotifynderManager $notifynder, $eventName, $category_name = null, $values = []);

    /**
     * Deletegate events to categories
     *
     * @param  NotifynderManager $notifynder
     * @param  array             $data
     * @param  array             $events
     * @return mixed
     */
    public function delegate(NotifynderManager $notifynder, $data = [], array $events);

    /**
     * Boot The listeners
     *
     * @param array $listeners
     */
    public function boot(array $listeners);

    /**
     * Check if the fired method has some notifications
     * to send
     *
     * @param $notificationsResult
     * @return bool
     */
    public function hasNotificationToSend($notificationsResult);
}
