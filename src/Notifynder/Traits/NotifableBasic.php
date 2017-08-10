<?php

namespace Fenos\Notifynder\Traits;

use Fenos\Notifynder\Helpers\TypeChecker;

/**
 * Class NotifableBasic.
 */
trait NotifableBasic
{
    /**
     * Get the notifications Relationship without any eager loading.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\MorphMany
     */
    abstract public function getLazyNotificationRelation();

    /**
     * Get the notifications Relationship.
     *
     * @param array|bool $eagerLoad
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function getNotificationRelation($eagerLoad = null)
    {
        $with = [];
        if (is_null($eagerLoad)) {
            $eagerLoad = notifynder_config('eager_load', false);
        }

        if ($eagerLoad === true) {
            $with = ['category', 'from', 'to']; // all relations
        } elseif (is_array($eagerLoad)) {
            $with = $eagerLoad;
        }

        return $this->getLazyNotificationRelation()->with($with);
    }

    /**
     * Get a new NotifynderManager instance with the given category.
     *
     * @param string|int|\Fenos\Notifynder\Models\NotificationCategory $category
     * @return \Fenos\Notifynder\Managers\NotifynderManager
     */
    public function notifynder($category)
    {
        return app('notifynder')->category($category);
    }

    /**
     * Get a new NotifynderManager instance with the given category and $this as the sender.
     *
     * @param string|int|\Fenos\Notifynder\Models\NotificationCategory $category
     * @return \Fenos\Notifynder\Managers\NotifynderManager
     */
    public function sendNotificationFrom($category)
    {
        return $this->notifynder($category)->from($this);
    }

    /**
     * Get a new NotifynderManager instance with the given category and $this as the receiver.
     *
     * @param string|int|\Fenos\Notifynder\Models\NotificationCategory $category
     * @return \Fenos\Notifynder\Managers\NotifynderManager
     */
    public function sendNotificationTo($category)
    {
        return $this->notifynder($category)->to($this);
    }

    /**
     * Read a single Notification.
     *
     * @param int $notification
     * @return bool
     */
    public function readNotification($notification)
    {
        return $this->updateSingleReadStatus($notification, 1);
    }

    /**
     * Unread a single Notification.
     *
     * @param int $notification
     * @return bool
     */
    public function unreadNotification($notification)
    {
        return $this->updateSingleReadStatus($notification, 0);
    }

    /**
     * @param int $notification
     * @param int $value
     * @return bool
     */
    protected function updateSingleReadStatus($notification, $value)
    {
        if (! TypeChecker::isNotification($notification, false)) {
            $notification = $this->getLazyNotificationRelation()->findOrFail($notification);
        }

        if ($this->getLazyNotificationRelation()->where($notification->getKeyName(), $notification->getKey())->exists()) {
            return $value ? $notification->read() : $notification->unread();
        }

        return false;
    }

    /**
     * Read all Notifications.
     *
     * @return mixed
     */
    public function readAllNotifications()
    {
        return $this->getLazyNotificationRelation()->update(['read' => 1]);
    }

    /**
     * Unread all Notifications.
     *
     * @return mixed
     */
    public function unreadAllNotifications()
    {
        return $this->getLazyNotificationRelation()->update(['read' => 0]);
    }

    /**
     * Count unread notifications.
     *
     * @return int
     */
    public function countUnreadNotifications()
    {
        return $this->getLazyNotificationRelation()->byRead(0)->count();
    }

    /**
     * Get all Notifications ordered by creation and optional limit.
     *
     * @param null|int $limit
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotifications($limit = null, $order = 'desc')
    {
        $query = $this->getNotificationRelation()->orderBy('created_at', $order);
        if (! is_null($limit)) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get all unread Notifications.
     *
     * @param null|int $limit
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotificationsNotRead($limit = null, $order = 'desc')
    {
        $query = $this->getNotificationRelation()->byRead(0)->orderBy('created_at', $order);
        if (! is_null($limit)) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
