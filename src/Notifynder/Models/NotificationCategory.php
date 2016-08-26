<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    protected $fillable = [
        'name',
        'text',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function __construct(array $attributes)
    {
        $attributes = array_merge([
            'text' => '',
        ], $attributes);

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        $config = app('notifynder.config');
        $model = $config->getNotificationModel();

        return $this->hasMany($model, 'category_id');
    }

    public function setNameAttribute($value)
    {
        $parts = explode('.', $value);
        foreach ($parts as $i => $part) {
            $parts[$i] = Str::slug(preg_replace('/[^a-z0-9_]/', '_', strtolower($part)), '_');
        }
        $this->attributes['name'] = implode('.', $parts);
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
        if ($category instanceof self) {
            $categoryId = $category->getKey();
        } elseif (! is_numeric($category)) {
            $categoryId = self::byName($category)->firstOrFail()->getKey();
        }

        return $categoryId;
    }
}
