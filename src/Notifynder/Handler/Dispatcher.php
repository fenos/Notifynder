<?php

namespace Fenos\Notifynder\Handler;

use Fenos\Notifynder\Contracts\NotifynderDispatcher;
use Fenos\Notifynder\Notifynder;
use Illuminate\Contracts\Events\Dispatcher as LaravelDispatcher;

/**
 * Class Dispatcher.
 */
class Dispatcher implements NotifynderDispatcher
{
    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $event;

    /**
     * Default namespace for notifications events.
     *
     * @var string
     */
    public static $defaultWildcard = 'Notifynder';

    /**
     * Default sender method.
     *
     * @var string
     */
    protected $sender = 'send';

    /**
     * @param LaravelDispatcher $event
     */
    public function __construct(LaravelDispatcher $event)
    {
        $this->event = $event;
    }

    /**
     * It fire the event associated to the passed key,
     * trigger the listener method bound with.
     *
     * @param  Notifynder        $notifynder
     * @param  string            $eventName
     * @param  string            $categoryName
     * @param  mixed|null        $values
     * @return mixed|null
     */
    public function fire(Notifynder $notifynder, $eventName, $categoryName = null, $values = [])
    {
        // Generate the event from the name given
        $eventName = $this->generateEventName($eventName);

        // Instantiate the Notifynder event Object that will provide
        // nice way to get your data on the handler. It will be the first
        // parameter
        $event = new NotifynderEvent($eventName, $categoryName, $values);

        // Fire the event given expecting an array of notifications or false
        // value to not send the notification
        $notificationsResult = $this->event->fire($eventName, [$event, $notifynder]);

        // if the event return an array of notifications then it will
        // send automatically
        if ($this->hasNotificationToSend($notificationsResult)) {

            // Send the notification with the sender
            // method specified in the property
            return call_user_func_array(
                [$notifynder, $this->sender],
                [$notificationsResult[0]]
            );
        }
    }

    /**
     * Delegate events to categories.
     *
     * @param  Notifynder        $notifynder
     * @param  array             $data
     * @param  array             $events
     * @return mixed
     */
    public function delegate(Notifynder $notifynder, $data, array $events)
    {
        foreach ($events as $category => $event) {
            $this->fire($notifynder, $event, $category, $data);
        }
    }

    /**
     * Tell the dispatcher to send
     * the notification with a custom
     * (extended method).
     *
     * @param $customMethod
     * @return $this
     */
    public function sendWith($customMethod)
    {
        $this->sender = $customMethod;

        return $this;
    }

    /**
     * Boot The listeners.
     *
     * @param array $listeners
     */
    public function boot(array $listeners)
    {
        if (count($listeners) > 0) {
            foreach ($listeners as $key => $listener) {
                // Notifynder.name.*
                $event = $this->generateEventName($key);
                $this->event->listen($event, $listener);
            }
        }
    }

    /**
     * Check if the fired method has some notifications
     * to send.
     *
     * @param $notificationsResult
     * @return bool
     */
    public function hasNotificationToSend($notificationsResult)
    {
        return is_array($notificationsResult)
                and count($notificationsResult) > 0
                and $notificationsResult[0] !== false
                and count($notificationsResult[0]) > 0;
    }

    /**
     * Get Event name.
     *
     * @param $eventName
     * @return string
     */
    protected function generateEventName($eventName)
    {
        return static::$defaultWildcard.'.'.str_replace('@', '.', $eventName);
    }
}
