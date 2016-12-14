<?php

namespace Fenos\Notifynder;

use App;
use Config;

/**
 * Class NotifynderTrait.
 *
 * To Import on the Model Associated
 */
trait NotifynderTrait
{
    /**
     * Notification Relation.
     *
     * @return mixed
     */
    public function notifications()
    {
        // check if on the configurations file there is the option
        // polymorphic setted to true, if so Notifynder will work
        // polymorphic.
        if (Config::get('notifynder::config.polymorphic') === false) {
            return $this->morphMany(Config::get('notifynder::config.model'), 'to');
        } else {
            return $this->hasMany(Config::get('notifynder::config.model'), 'to_id');
        }
    }

    /**
     * @return \Fenos\Notifynder\Notifynder
     */
    public function notifynderInstance()
    {
        return App::make('notifynder');
    }

    /**
     * Read all notification.
     *
     * @return mixed
     */
    public function readAll()
    {
        return $this->notifynderIntance()->entity(get_class($this))->readAll($this->id);
    }

    /**
     * Read Limit.
     *
     * @param int    $numbers
     * @param string $order
     *
     * @return mixed
     */
    public function readLimit($numbers = 10, $order = 'ASC')
    {
        return $this->notifynderInstance()->entity(get_class($this))->readLimit($this->id, $numbers, $order);
    }

    /**
     * Delete Limit.
     *
     * @param int    $numbers
     * @param string $order
     *
     * @return mixed
     */
    public function deleteLimit($numbers = 10, $order = 'ASC')
    {
        return $this->notifynderInstance()->entity(get_class($this))->deleteLimit($this->id, $numbers, $order);
    }

    /**
     * Delete all.
     *
     * @return bool
     */
    public function deleteAll()
    {
        return $this->notifynderInstance()->entity(get_class($this))->deleteAll($this->id);
    }

    /**
     * Get Not Read.
     *
     * @param null $limit
     * @param bool $paginate
     *
     * @return mixed
     */
    public function getNotRead($limit = null, $paginate = false)
    {
        return $this->notifynderInstance()->entity(get_class($this))->getNotRead($this->id, $limit, $paginate);
    }

    /**
     * Get all notifications.
     *
     * @param null $limit
     * @param bool $paginate
     *
     * @return mixed
     */
    public function getAll($limit = null, $paginate = false)
    {
        return $this->notifynderInstance()->entity(get_class($this))->getAll($this->id, $limit, $paginate);
    }

    /**
     * Count Not read notification.
     *
     * @return mixed
     */
    public function countNotRead()
    {
        return $this->notifynderInstance()->entity(get_class($this))->countNotRead($this->id);
    }
}
