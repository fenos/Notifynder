<?php

namespace Fenos\Notifynder\Handler;

use Fenos\Notifynder\Contracts\NotifyListener;
use Fenos\Notifynder\Notifynder;

/**
 * Class NotifynderHandler.
 */
class NotifynderHandler
{
    /**
     * Handle the event.
     *
     * @param NotifyListener  $eventListener
     * @param null            $notifynder
     * @return mixed
     */
    public function handle(NotifyListener $eventListener, $notifynder = null)
    {
        $event = $eventListener->getNotifynderEvent();

        $eventName = $this->getEventName($event->getEvent());

        if ($this->listenerIsRegistered($eventName)) {

            // Make sure a notifynder instance is passed to the event
            // invoker method
            $notifynder = (is_null($notifynder)) ? $this->getNotifynder() : $notifynder;

            // Build the notifications
            $builtNotifications = call_user_func_array([$this, $eventName], [$event, $notifynder]);

            // If the listener is the NotifynderEvent that means
            // we are triggering this from the Notifynder::fire()
            // Event, it will take care of sending the notification
            if ($eventListener instanceof NotifynderEvent) {
                return $builtNotifications;
            }

            // Event has been dispatched manually from the native
            // Laravel eventing system then I'll send the notification
            // Right here
            if ($this->hasNotificationToSend([$builtNotifications])) {
                return $notifynder->send($builtNotifications);
            }
        }
    }

    /**
     * Check if the listener exists on the class
     * adding when as convention.
     *
     * ['postAdd'] whenPostAdd]
     *
     * @param $eventName
     * @return bool
     */
    protected function listenerIsRegistered($eventName)
    {
        return method_exists($this, $eventName);
    }

    /**
     * Get Event Name from the key
     * it use a convention.
     *
     * given user.post.add -> postAdd
     * given user@postAdd -> postAdd
     *
     * @param $event
     * @return string
     */
    protected function getEventName($event)
    {
        // Remove the Notifynder namespaces for
        // the find the method
        $event = str_replace(Dispatcher::$defaultWildcard.'.', '', $event);

        $eventNameSpace = (strpos($event, '@'))
            ? explode('@', $event)
            : explode('.', $event);

        // Check if the name has been splitted in 2
        if (count($eventNameSpace) > 1) {
            array_shift($eventNameSpace);
        }

        $nameMethod = implode('_', $eventNameSpace);

        return camel_case($nameMethod);
    }

    /**
     * Get Notifynder Instance.
     *
     * @return Notifynder
     */
    protected function getNotifynder()
    {
        $notifynder = app('notifynder');

        return $notifynder;
    }

    /**
     * Check if the fired method has some notifications
     * to send.
     *
     * @param $notificationsResult
     * @return bool
     */
    protected function hasNotificationToSend($notificationsResult)
    {
        return is_array($notificationsResult)
        and count($notificationsResult) > 0
        and $notificationsResult[0] !== false
        and count($notificationsResult[0]) > 0;
    }
}
