<?php namespace Fenos\Notifynder\Handler; 

use Fenos\Notifynder\Contracts\NotifynderDispatcher;
use Fenos\Notifynder\NotifynderManager;
use Illuminate\Contracts\Events\Dispatcher as LaravelDispatcher;

class Dispatcher implements NotifynderDispatcher {

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    private $event;

    /**
     * Default namespace for notifications events
     *
     * @var string
     */
    protected $defaultWildcard = 'Notifynder';

    /**
     * @param LaravelDispatcher     $event
     */
    function __construct(LaravelDispatcher $event)
    {
        $this->event = $event;
    }

    /**
     * It fire the event associated to the passed key,
     * trigging the listener method bound with
     *
     * @param NotifynderManager    $notifynder
     * @param  string       $eventName
     * @param  string       $category_name
     * @param  mixed|null   $values
     * @return mixed|null
     */
    public function fire(NotifynderManager $notifynder, $eventName, $category_name = null, $values = [])
    {
        // Generete the event from the name given
        $eventName = $this->generateEventName($eventName);

        // Instantiate the Notifynder event Object that will provide
        // nice way to get your data on the handler. It will be the first
        // parameter
        $event = new NotifynderEvent($eventName,$category_name,$values);

        // Fire the event given expecting an array of notifications or falsy
        // value to not send the notification
        $notificationsResult = $this->event->fire($eventName,[$event,$notifynder]);

        // if the event return an array of notifications then it will
        // send automatically
        if ($this->hasNotificationToSend($notificationsResult))
        {
            return $notifynder->send($notificationsResult[0]);
        }

        return null;
    }

    /**
     * Deletegate events to categories
     *
     * @param NotifynderManager $notifynder
     * @param array      $data
     * @param array      $events
     * @return mixed
     */
    public function delegate(NotifynderManager $notifynder, $data = [],array $events)
    {
        foreach($events as $category => $event)
        {
           $this->fire($notifynder,$event,$category,$data);
        }
    }

    /**
     * Boot The listeners
     *
     * @param array $listeners
     */
    public function boot(array $listeners)
    {
        if (count($listeners) > 0)
        {
            foreach($listeners as $key => $listener)
            {
                // Notifynder.name.*
                $event = $this->generateEventName($key);
                $this->event->listen($event,$listener);
            }
        }
    }

    /**
     * Check if the fired method has some notifications
     * to send
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
     * Get Event name
     *
     * @param $eventName
     * @return string
     */
    protected function generateEventName($eventName)
    {
        return $this->defaultWildcard . "." . str_replace('@', '.', $eventName);
    }

}