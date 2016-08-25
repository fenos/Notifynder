<?php

namespace Fenos\Notifynder\Traits;

use Fenos\Notifynder\Helpers\TypeChecker;

/**
 * Class Notifable.
 */
trait Notifable
{
    /**
     * Get the notifications Relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        $model = notifynder_config()->getNotificationModel();
        if (notifynder_config()->isPolymorphic()) {
            return $this->morphMany($model, 'to');
        }

        return $this->hasMany($model, 'to_id');
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
        if (! TypeChecker::isNotification($notification, false)) {
            $notification = $this->notifications()->firstOrFail($notification);
        }

        if ($this->notifications()->where($notification->getKeyName(), $notification->getKey())->exists()) {
            return $notification->read();
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
        return $this->notifications()->update(['read' => 1]);
    }

    /**
     * Unread a single Notification.
     *
     * @param int $notification
     * @return bool
     */
    public function unreadNotification($notification)
    {
        if (! TypeChecker::isNotification($notification, false)) {
            $notification = $this->notifications()->firstOrFail($notification);
        }

        if ($this->notifications()->where($notification->getKeyName(), $notification->getKey())->exists()) {
            return $notification->unread();
        }

        return false;
    }

    /**
     * Unread all Notifications.
     *
     * @return mixed
     */
    public function unreadAllNotifications()
    {
        return $this->notifications()->update(['read' => 0]);
    }

    /**
     * Count unread notifications.
     *
     * @return int
     */
    public function countUnreadNotifications()
    {
        return $this->notifications()->byRead(0)->count();
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
        $query = $this->notifications()->orderBy('created_at', $order);
        if (! is_null($limit)) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
