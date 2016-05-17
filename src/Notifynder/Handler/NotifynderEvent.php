<?php

namespace Fenos\Notifynder\Handler;

use Fenos\Notifynder\Contracts\NotifyListener;

/**
 * Class NotifynderEvent.
 */
class NotifynderEvent implements NotifyListener
{
    /**
     * @var string
     */
    protected $event;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var string
     */
    protected $category;

    /**
     * @param       $event
     * @param       $category
     * @param array $values
     */
    public function __construct($event, $category = null, array $values = [])
    {
        $this->event = $event;
        $this->values = $values;
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get a value from the given
     * values.
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->values)) {
            return $this->values[$name];
        }
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @return NotifynderEvent
     */
    public function getNotifynderEvent()
    {
        return $this;
    }
}
