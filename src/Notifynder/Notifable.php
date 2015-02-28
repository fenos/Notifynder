<?php namespace Fenos\Notifynder;

/**
 * Class Notifable
 *
 * Trait to implement in your models
 * that want to be notified, it will set relations
 * and nice short cut for the management of notifications
 *
 * @package Fenos\Notifynder
 */
trait Notifable {

    /**
     * Notification Relation
     *
     * @return mixed
     */
    public function notifications()
    {
        // check if on the configurations file there is the option
        // polymorphic setted to true, if so Notifynder will work
        // polymorphic.
        if ( config('notifynder.polymorphic') == false )
        {
            return $this->morphMany(config('notifynder.notification_model'),'to');
        }
        else {
            return $this->hasMany(config('notifynder.notification_model'),'to_id');
        }
    }
    /**
     * @return \Fenos\Notifynder\NotifynderManager
     */
    protected function notifynderInstance()
    {
        return app('notifynder');
    }
    /**
     * Read all notification
     *
     * @return mixed
     */
    public function readAll()
    {
        return $this->notifynderIntance()->entity(
            get_class($this)
        )->readAll($this->id);
    }
    /**
     * Read Limit
     *
     * @param int    $numbers
     * @param string $order
     * @return mixed
     */
    public function readLimit($numbers = 10, $order = "ASC")
    {
        return $this->notifynderInstance()->entity(
            get_class($this)
        )->readLimit($this->id,$numbers,$order);
    }
    /**
     * Delete Limit
     *
     * @param int    $numbers
     * @param string $order
     * @return mixed
     */
    public function deleteLimit($numbers = 10, $order = "ASC")
    {
        return $this->notifynderInstance()->entity(get_class($this))->deleteLimit($this->id,$numbers,$order);
    }
    /**
     * Delete all
     *
     * @return Bool
     */
    public function deleteAll()
    {
        return $this->notifynderInstance()->entity(
            get_class($this)
        )->deleteAll($this->id);
    }

    /**
     * Get Not Read
     *
     * @param null   $limit
     * @param bool   $paginate
     * @param string $order
     * @return mixed
     */
    public function getNotRead($limit = null, $paginate = false,$order = 'desc')
    {
        return $this->notifynderInstance()->entity(
            get_class($this)
        )->getNotRead($this->id,$limit,$paginate,$order);
    }

    /**
     * Get all notifications
     *
     * @param null   $limit
     * @param bool   $paginate
     * @param string $order
     * @return mixed
     */
    public function getAll($limit = null, $paginate = false, $order = 'desc')
    {
        return $this->notifynderInstance()->entity(
            get_class($this)
        )->getAll($this->id,$limit,$paginate,$order);
    }
    /**
     * Count Not read notification
     *
     * @return mixed
     */
    public function countNotRead()
    {
        return $this->notifynderInstance()->entity(
            get_class($this)
        )->countNotRead($this->id);
    }
}