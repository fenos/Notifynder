<?php

namespace Fenos\Notifynder;

use Closure;

/**
 * Class Notifable.
 *
 * Trait to implement in your models
 * that want to be notified, it will set relations
 * and nice short cut for the management of notifications
 */
trait Notifable
{
    /**
     * Notification Relation.
     *
     * @return mixed
     */
    public function notifications()
    {
        // check if on the configurations file there is the option
        // polymorphic set to true, if so Notifynder will work
        // polymorphic.
        if (config('notifynder.polymorphic') == false) {
            return $this->morphMany(config('notifynder.notification_model'), 'to');
        } else {
            return $this->hasMany(config('notifynder.notification_model'), 'to_id');
        }
    }

    /**
     * Read all Notifications.
     *
     * @return mixed
     */
    public function readAllNotifications()
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->readAll($this->getKey());
    }

    /**
     * Read Limiting Notifications.
     *
     * @param  int    $numbers
     * @param  string $order
     * @return mixed
     */
    public function readLimitNotifications($numbers = 10, $order = 'ASC')
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->readLimit($this->getKey(), $numbers, $order);
    }

    /**
     * Delete Limiting Notifications.
     *
     * @param  int    $numbers
     * @param  string $order
     * @return mixed
     */
    public function deleteLimitNotifications($numbers = 10, $order = 'ASC')
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->deleteLimit($this->getKey(), $numbers, $order);
    }

    /**
     * Delete all Notifications.
     *
     * @return bool
     */
    public function deleteAllNotifications()
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->deleteAll($this->getKey());
    }

    /**
     * Get Not Read.
     *
     * @param  null     $limit
     * @param  int|null $paginate
     * @param  string   $order
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getNotificationsNotRead($limit = null, $paginate = null, $order = 'desc', Closure $filterScope = null)
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->getNotRead($this->getKey(), $limit, $paginate, $order, $filterScope);
    }

    /**
     * Get all notifications.
     *
     * @param  null     $limit
     * @param  int|null $paginate
     * @param  string   $order
     * @param Closure   $filterScope
     * @return mixed
     */
    public function getNotifications($limit = null, $paginate = null, $order = 'desc', Closure $filterScope = null)
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->getAll($this->getKey(), $limit, $paginate, $order, $filterScope);
    }

    /**
     * Get last notification.
     *
     * @param null    $category
     * @param Closure $filterScope
     * @return mixed
     */
    public function getLastNotification($category = null, Closure $filterScope = null)
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->getLastNotification($this->getKey(), $category, $filterScope);
    }

    /**
     * Count Not read notification.
     *
     * @param Closure $filterScope
     * @return mixed
     */
    public function countNotificationsNotRead(Closure $filterScope = null)
    {
        return $this->notifynderInstance()->entity(
            $this->getMorphClass()
        )->countNotRead($this->getKey(), $filterScope);
    }

    /**
     * @return \Fenos\Notifynder\NotifynderManager
     */
    protected function notifynderInstance()
    {
        return app('notifynder');
    }
}
