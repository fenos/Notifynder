<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationCategory
 * @package Fenos\Notifynder\Models
 */
class NotificationCategory extends Model
{
    /**
     * @var string
     */
    protected $table = 'notification_categories';

    /**
     * @var array
     */
    protected $fillable = ['name', 'text'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        $config = app('notifynder.config');
        $model = $config->getNotificationModel();

        return $this->hasMany($model, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(
            NotificationGroup::class,
            'notifications_categories_in_groups',
            'category_id',
            'group_id'
        );
    }

    /**
     * @param Builder $query
     * @param $name
     * @return Builder
     */
    public function scopeByName(Builder $query, $name)
    {
        return $query->where('name', $name);
    }
}
