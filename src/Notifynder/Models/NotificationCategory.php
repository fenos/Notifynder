<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NotificationCategory extends Model
{
    protected $table = 'notification_categories';

    protected $fillable = ['name', 'text'];

    public $timestamps = false;

    public function notifications()
    {
        $config = app('notifynder.config');
        $model = $config->getNotificationModel();

        return $this->hasMany($model, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            NotificationGroup::class,
            'notifications_categories_in_groups',
            'category_id',
            'group_id'
        );
    }

    public function scopeByName(Builder $query, $name)
    {
        return $query->where('name', $name);
    }
}
