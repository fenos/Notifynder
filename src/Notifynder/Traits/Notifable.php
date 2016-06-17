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
}
