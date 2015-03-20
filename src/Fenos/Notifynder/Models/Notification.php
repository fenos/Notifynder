<?php namespace Fenos\Notifynder\Models;

use Config;
use Fenos\Notifynder\Models\Collection\NotifynderCollection;
use Fenos\Notifynder\Parse\NotifynderParse;
use Fenos\Notifynder\Translator\NotifynderTranslator;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
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
    protected $fillable = ['to_id','to_type','from_id','from_type','category_id','read','url','extra'];

    /**
     * Custom Collection
     */
    public function newCollection(array $models = array())
    {
        return new NotifynderCollection($models, new NotifynderTranslator());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function body()
    {
        return $this->belongsTo('Fenos\Notifynder\Models\NotificationCategory', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function from()
    {
        // check if on the configurations file there is the option
        // polymorphic setted to true, if so Notifynder will work
        // polymorphic.
        if (Config::get('notifynder::config.polymorphic') === false) {
            return $this->belongsTo(Config::get('notifynder::config.model'), 'from_id');
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
        if (Config::get('notifynder::config.polymorphic') === false) {
            return $this->belongsTo(Config::get('notifynder::config.model'), 'to_id');
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
     * Where Polymorphic
     *
     * @param $query
     * @param $table_id
     * @param $table_type
     * @param $table_id_value
     * @param $table_type_value
     * @param $isBuilder
     * @return mixed
     */
    public function scopeWherePolymorphic($query, $table_id, $table_type, $table_id_value, $table_type_value, $isBuilder = null)
    {
        if (!is_null($isBuilder)) {
            $query = $isBuilder;
        }

        if (! $table_type_value) {
            return $query->where($table_id, $table_id_value);
        } else {
            return $query->where($table_id, $table_id_value)
                ->where($table_type, $table_type_value);
        }
    }

    /**
     * @return mixed
     */
    public function parse()
    {
        (new NotifynderParse($this))->parse();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotifyBodyAttribute()
    {
        return (new NotifynderParse($this, $this->extra))->parse();
    }
}
