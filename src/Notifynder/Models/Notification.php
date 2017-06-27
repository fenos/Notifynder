<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Fenos\Notifynder\Parsers\NotificationParser;
use Fenos\Notifynder\Builder\Notification as BuilderNotification;

/**
 * Class Notification.
 *
 * @property int $to_id
 * @property string $to_type
 * @property int $from_id
 * @property string $from_type
 * @property int $category_id
 * @property bool $read
 * @property string $url
 * @property array $extra
 * @property string $expires_at
 * @property int $stack_id
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
     * @param array|\Fenos\Notifynder\Builder\Notification $attributes
     */
    public function __construct($attributes = [])
    {
        $table = app('notifynder.resolver.model')->getTable(get_class($this));
        if (! empty($table)) {
            $this->setTable($table);
        }

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
        $fillables = array_unique(array_merge($this->getFillable(), $this->getCustomFillableFields()));

        return $fillables;
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
        $categoryId = NotificationCategory::getIdByCategory($category);

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
