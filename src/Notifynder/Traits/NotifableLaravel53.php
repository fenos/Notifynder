<?php

namespace Fenos\Notifynder\Traits;

/**
 * Class Notifable.
 */
trait NotifableLaravel53
{
    use NotifableBasic;

    /**
     * Get the notifications Relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifynderNotifications()
    {
        $model = notifynder_config()->getNotificationModel();
        if (notifynder_config()->isPolymorphic()) {
            return $this->morphMany($model, 'to');
        }

        return $this->hasMany($model, 'to_id');
    }

    /**
     * Get the notifications Relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function getNotificationRelation()
    {
        return $this->notifynderNotifications();
    }
}
