<?php

namespace Fenos\Notifynder\Models;

use Fenos\Notifynder\Builder\Notification as BuilderNotification;
use Fenos\Notifynder\Parsers\NotificationParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification.
 */
class Notification extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'to_id',
        'to_type',
        'from_id',
        'from_type',
        'category_id',
        'read',
        'url',
        'extra',
        'expires_at',
        'stack_id',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'text',
        'template_body',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
    ];

    /**
     * Notification constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->fillable($this->mergeFillables());

        if ($attributes instanceof BuilderNotification) {
            $attributes = $attributes->toArray();
        }

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(NotificationCategory::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function from()
    {
        if (notifynder_config()->isPolymorphic()) {
            return $this->morphTo('from');
        }

        return $this->belongsTo(notifynder_config()->getNotifiedModel(), 'from_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function to()
    {
        if (notifynder_config()->isPolymorphic()) {
            return $this->morphTo('to');
        }

        return $this->belongsTo(notifynder_config()->getNotifiedModel(), 'to_id');
    }

    /**
     * @return array
     */
    public function getCustomFillableFields()
    {
        return notifynder_config()->getAdditionalFields();
    }

    /**
     * @return array
     */
    protected function mergeFillables()
    {
        $fillables = array_unique($this->getFillable() + $this->getCustomFillableFields());

        return $fillables;
    }

    /**
     * @return string
     */
    public function getTemplateBodyAttribute()
    {
        if (notifynder_config()->isTranslated()) {
            $key = notifynder_config()->getTranslationDomain().'.'.$this->category->name;
            $trans = trans($key);
            if ($trans != $key) {
                return $trans;
            }
        }

        return $this->category->text;
    }

    /**
     * @return string
     * @throws \Fenos\Notifynder\Exceptions\ExtraParamsException
     */
    public function getTextAttribute()
    {
        if (! array_key_exists('text', $this->attributes)) {
            $notifynderParse = new NotificationParser();
            $this->attributes['text'] = $notifynderParse->parse($this);
        }

        return $this->attributes['text'];
    }

    /**
     * @return bool|int
     */
    public function read()
    {
        return $this->update(['read' => 1]);
    }

    /**
     * @return bool|int
     */
    public function unread()
    {
        return $this->update(['read' => 0]);
    }

    /**
     * @return bool
     */
    public function resend()
    {
        $this->updateTimestamps();
        $this->read = 0;

        return $this->save();
    }

    /**
     * @return bool
     */
    public function isAnonymous()
    {
        return is_null($this->from_id);
    }

    /**
     * @param Builder $query
     * @param $category
     * @return Builder
     */
    public function scopeByCategory(Builder $query, $category)
    {
        $categoryId = $category;
        if ($category instanceof NotificationCategory) {
            $categoryId = $category->getKey();
        } elseif (! is_numeric($category)) {
            $categoryId = NotificationCategory::byName($category)->firstOrFail()->getKey();
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * @param Builder $query
     * @param int $read
     * @return Builder
     */
    public function scopeByRead(Builder $query, $read = 1)
    {
        return $query->where('read', $read);
    }
}
