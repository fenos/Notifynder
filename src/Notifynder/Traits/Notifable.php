<?php

namespace Fenos\Notifynder\Traits;

use Fenos\Notifynder\Helpers\TypeChecker;
use Fenos\Notifynder\Models\Notification;

trait Notifable
{
    public function notifications()
    {
        $model = notifynder_config()->getNotificationModel();
        if (notifynder_config()->isPolymorphic()) {
            return $this->morphMany($model, 'to');
        }

        return $this->hasMany($model, 'to_id');
    }

    public function notifynder($category)
    {
        return app('notifynder')->category($category);
    }

    public function sendNotificationFrom($category)
    {
        return $this->notifynder($category)->from($this);
    }

    public function sendNotificationTo($category)
    {
        return $this->notifynder($category)->to($this);
    }

    public function readNotification($notification)
    {
        if (!TypeChecker::isNotification($notification, false)) {
            $notification = Notification::firstOrFail($notification);
        }

        if($this->notifications()->where($notification->getKeyName(), $notification->getKey())->exists()) {
            return $notification->read();
        }
        return false;
    }

    public function readAllNotifications()
    {
        return $this->notifications()->update(['read' => 1]);
    }

    public function unreadNotification($notification)
    {
        if (!TypeChecker::isNotification($notification, false)) {
            $notification = Notification::firstOrFail($notification);
        }

        if($this->notifications()->where($notification->getKeyName(), $notification->getKey())->exists()) {
            return $notification->unread();
        }
        return false;
    }

    public function countUnreadNotifications()
    {
        return $this->notifications()->byRead(0)->count();
    }

    public function getNotifications($limit = null, $order = 'desc')
    {
        $query = $this->notifications()->orderBy('created_at', $order);
        if (! is_null($limit)) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
