<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class NotificationCategory.
 *
 * @property string $name
 * @property string $text
 * @method Builder byName($name)
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
     * @var array
     */
    protected $appends = [
        'template_body',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        $table = app('notifynder.resolver.model')->getTable(get_class($this));
        if (! empty($table)) {
            $this->setTable($table);
        }

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
        $model = app('notifynder.resolver.model')->getModel(Notification::class);

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
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */
    public function getTemplateBodyAttribute()
    {
        if (notifynder_config()->isTranslated()) {
            $key = notifynder_config()->getTranslationDomain().'.'.$this->name;
            $trans = trans($key);
            if ($trans != $key) {
                return $trans;
            }
        }

        return $this->text;
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
