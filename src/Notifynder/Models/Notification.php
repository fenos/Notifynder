<?php

namespace Fenos\Notifynder\Models;

use Fenos\Notifynder\Notifications\ExtraParams;
use Fenos\Notifynder\Parsers\NotifynderParser;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Class Notification.
 *
 * @property int to_id
 * @property string to_type
 * @property int from_id
 * @property string from_type
 * @property int category_id
 * @property int read
 * @property string url
 * @property string extra
 *
 * Php spec complain when model is mocked
 * if I turn them on as php doc block
 *
 * @method wherePolymorphic
 * @method withNotRead
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
        'expire_time',
        'stack_id',
    ];

    /**
     * Notification constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $fillables = $this->mergeFillable();
        $this->fillable($fillables);

        parent::__construct($attributes);
    }

    /**
     * Custom Collection.
     *
     * @param  array                                                         $models
     * @return NotifynderCollection|\Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new NotifynderCollection($models);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function body()
    {
        return $this->belongsTo(NotificationCategory::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function from()
    {
        // check if on the configurations file there is the option
        // polymorphic set to true, if so Notifynder will work
        // polymorphic.
        if (config('notifynder.polymorphic') == false) {
            return $this->belongsTo(config('notifynder.model'), 'from_id');
        }

        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function to()
    {
        // check if on the configurations file there is the option
        // polymorphic set to true, if so Notifynder will work
        // polymorphic.
        if (config('notifynder.polymorphic') == false) {
            return $this->belongsTo(config('notifynder.model'), 'to_id');
        }

        return $this->morphTo();
    }

    /**
     * Not read scope.
     *
     * @param $query
     * @return mixed
     */
    public function scopeWithNotRead($query)
    {
        return $query->where('read', 0);
    }

    /**
     * Only Expired Notification scope.
     *
     * @param $query
     * @return mixed
     */
    public function scopeOnlyExpired($query)
    {
        return $query->where('expire_time', '<', Carbon::now());
    }

    /**
     * Where Polymorphic.
     *
     * @param $query
     * @param $toId
     * @param $type
     * @return mixed
     */
    public function scopeWherePolymorphic($query, $toId, $type)
    {
        if (! $type or config('notifynder.polymorphic') === false) {
            return $query->where('to_id', $toId);
        }

        return $query->where('to_id', $toId)
            ->where('to_type', $type);
    }

    /**
     * Get parsed body attributes.
     *
     * @return string
     */
    public function getNotifyBodyAttribute()
    {
        $notifynderParse = new NotifynderParser();

        return $notifynderParse->parse($this);
    }

    /**
     * Get parsed body attributes.
     *
     * @return string
     */
    public function getTextAttribute()
    {
        return $this->notify_body;
    }

    /**
     * @param $value
     * @return \Fenos\Notifynder\Notifications\ExtraParams
     */
    public function getExtraAttribute($value)
    {
        if (! empty($value)) {
            return new ExtraParams($value);
        }

        return new ExtraParams([]);
    }

    /**
     * Filter Scope by category.
     *
     * @param $query
     * @param $category
     * @return mixed
     */
    public function scopeByCategory($query, $category)
    {
        if (is_numeric($category)) {
            return $query->where('category_id', $category);
        }

        return $query->whereHas('body', function ($categoryQuery) use ($category) {
            $categoryQuery->where('name', $category);
        });
    }

    /**
     * Get custom required fields from the configs
     * so that we can automatically bind them to the model
     * fillable property.
     *
     * @return mixed
     */
    public function getCustomFillableFields()
    {
        if (function_exists('app') && app() instanceof Container) {
            return Arr::flatten(config('notifynder.additional_fields', []));
        }

        return [];
    }

    /**
     * @return array
     */
    protected function mergeFillable()
    {
        $fillables = array_unique(array_merge($this->getFillable(), $this->getCustomFillableFields()));

        return $fillables;
    }

    /**
     * Filter Scope by stack.
     *
     * @param $query
     * @param $stackId
     * @return mixed
     */
    public function scopeByStack($query, $stackId)
    {
        return $query->where('stack_id', $stackId);
    }

    /**
     * Check if this notification is part of a stack.
     *
     * @return bool
     */
    public function hasStack()
    {
        return ! is_null($this->stack_id);
    }

    /**
     * Get the full stack of notifications if this has one.
     *
     * @return null|Collection
     */
    public function getStack()
    {
        if ($this->hasStack()) {
            return static::byStack($this->stack_id)->get();
        }
    }
}
