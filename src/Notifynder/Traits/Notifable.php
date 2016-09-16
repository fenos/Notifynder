<?php

namespace Fenos\Notifynder\Traits;

/**
 * Class Notifable.
 */
trait Notifable
{
    use NotifableBasic;

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

    public function getNotificationRelation()
    {
        return $this->notifications();
    }
}
