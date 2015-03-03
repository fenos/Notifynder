<?php namespace Fenos\Notifynder\Handler;

class NotifynderHandler
{

    /**
     * Handle the event to the given
     *
     * @param $event
     * @param $notifynder
     * @return mixed
     */
    public function handle(NotifynderEvent $event, $notifynder)
    {
        $eventName = $this->getEventName($event->getEvent());

        if ($this->listenerIsRegistered($eventName)) {
            return call_user_func_array([$this, $eventName], [$event, $notifynder]);
        }

        return;
    }

    /**
     * Check if the listener exists on the class
     * adding when as convention
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
     * it use a convention
     *
     * given user.post.add -> postAdd
     * given user@postAdd -> postAdd
     *
     * @param $event
     * @return mixed
     */
    protected function getEventName($event)
    {
        // Remove the Notiynder namespaces for
        // the find the method
        $event = str_replace('Notifynder.', '', $event);

        $eventNameSpace = (strpos($event, '@'))
            ? explode('@', $event)
            : explode('.', $event);

        array_shift($eventNameSpace);

        $nameMethod = implode('_', $eventNameSpace);

        return camel_case($nameMethod);
    }
}
