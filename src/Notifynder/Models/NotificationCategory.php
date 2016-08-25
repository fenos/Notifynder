<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationCategory.
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

    /**
     * @param string|int|\Fenos\Notifynder\Models\NotificationCategory $category
     * @return int
     */
    public static function getIdByCategory($category)
    {
        $categoryId = $category;
        if ($category instanceof NotificationCategory) {
            $categoryId = $category->getKey();
        } elseif (! is_numeric($category)) {
            $categoryId = NotificationCategory::byName($category)->firstOrFail()->getKey();
        }

        return $categoryId;
    }
}
