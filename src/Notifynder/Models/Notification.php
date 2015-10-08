<?php namespace Fenos\Notifynder\Models;

use Fenos\Notifynder\Notifications\ExtraParams;
use Fenos\Notifynder\Parsers\NotifynderParser;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Notification
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
 *
 * @package Fenos\Notifynder\Models
 */
class Notification extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'to_id','to_type','from_id','from_type',
        'category_id','read','url','extra', 'expire_time',
    ];

    /**
     * Custom Collection
     *
     * @param  array                                                         $models
     * @return NotifynderCollection|\Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = array())
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
        // polymorphic setted to true, if so Notifynder will work
        // polymorphic.
        if (config('notifynder.polymorphic') == false) {
            return $this->belongsTo(config('notifynder.model'), 'from_id');
        } else {
            return $this->morphTo();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function to()
    {
        // check if on the configurations file there is the option
        // polymorphic setted to true, if so Notifynder will work
        // polymorphic.
        if (config('notifynder.polymorphic') == false) {
            return $this->belongsTo(config('notifynder.model'), 'to_id');
        } else {
            return $this->morphTo();
        }
    }

    /**
     * Not read scope
     *
     * @param $query
     * @return mixed
     */
    public function scopeWithNotRead($query)
    {
        return $query->where('read', 0);
    }

    /**
     * Only Expired Notification scope
     *
     * @param $query
     * @return mixed
     */
    public function scopeOnlyExpired($query)
    {
        return $query->where('expire_time', '<', Carbon::now());
    }

    /**
     * Where Polymorphic
     *
     * @param $query
     * @param $id
     * @param $type
     * @return mixed
     */
    public function scopeWherePolymorphic($query, $id, $type)
    {
        if (! $type or config('notifynder.polymorphic') === false) {
            return $query->where('to_id', $id);
        } else {
            return $query->where('to_id', $id)
                ->where('to_type', $type);
        }
    }

    /**
     * Get parsed body attributes
     *
     * @return mixed
     */
    public function getNotifyBodyAttribute()
    {
        $notifynderParse = new NotifynderParser();

        return $notifynderParse->parse($this);
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function getExtraAttribute($value)
    {
        return new ExtraParams(json_decode($value));
    }

    /**
     * Filter Scope by category
     *
     * @param $query
     * @param $category
     * @return mixed
     */
    public function scopeByCategory($query,$category)
    {
        if (is_numeric($category)) {

            return $query->where('category_id',$category);
        }

        return $query->whereHas('body', function($categoryQuery) use ($category) {
            $categoryQuery->where('name',$category);
        });
    }
}
