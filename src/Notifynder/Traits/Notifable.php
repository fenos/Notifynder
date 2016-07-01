<?php

namespace Fenos\Notifynder\Traits;

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
}
