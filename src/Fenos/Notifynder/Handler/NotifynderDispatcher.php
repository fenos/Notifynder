<?php

namespace Fenos\Notifynder\Handler;

abstract class NotifynderDispatcher
{
    /**
     * Handle the event to the given.
     *
     * @param $event
     * @param $category_name
     * @param $notifynder
     *
     * @return mixed
     */
    public function handle($event, $category_name, $notifynder)
    {
        $eventName = $this->getEventName($event['eventName']);

        if ($this->listenerIsRegistered($eventName)) {
            unset($event['eventName']);

            return call_user_func_array([$this, $eventName], [$event, $category_name, $notifynder]);
        }
    }

    /**
     * Check if the listener exists on the class
     * adding when as convention.
     *
     * ['postAdd'] whenPostAdd]
     *
     * @param $eventName
     *
     * @return bool
     */
    public function listenerIsRegistered($eventName)
    {
        return method_exists($this, $eventName);
    }

    /**
     * Get Event Name from the key
     * it use a convention.
     *
     * given user.post.add -> postAdd
     *
     * @param $event
     *
     * @return mixed
     */
    public function getEventName($event)
    {
        $eventNameSpace = explode('.', $event);

        array_shift($eventNameSpace);

        $nameMethod = implode('_', $eventNameSpace);

        return camel_case($nameMethod);
    }
}
