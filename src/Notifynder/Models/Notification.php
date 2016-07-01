<?php

namespace Fenos\Notifynder\Models;

use Fenos\Notifynder\Builder\Notification as BuilderNotification;
use Fenos\Notifynder\Parsers\NotificationParser;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'to_id',
        'to_type',
        'from_id',
        'from_type',
        'category_id',
        'read',
        'url',
        'extra',
        'expire_time', // ToDo: rename to `expires_at`
        'stack_id',
    ];

    protected $appends = [
        'text',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function __construct($attributes = [])
    {
        $this->fillable($this->mergeFillables());

        if ($attributes instanceof BuilderNotification) {
            $attributes = $attributes->toArray();
        }

        parent::__construct($attributes);
    }

    public function category()
    {
        return $this->belongsTo(NotificationCategory::class, 'category_id');
    }

    public function from()
    {
        if (notifynder_config()->isPolymorphic()) {
            return $this->belongsTo(notifynder_config()->getModel(), 'from_id');
        }

        return $this->morphTo();
    }

    public function to()
    {
        if (notifynder_config()->isPolymorphic()) {
            return $this->belongsTo(notifynder_config()->getModel(), 'to_id');
        }

        return $this->morphTo();
    }

    public function getCustomFillableFields()
    {
        return notifynder_config()->getAdditionalFields();
    }

    protected function mergeFillables()
    {
        $fillables = array_unique($this->getFillable() + $this->getCustomFillableFields());

        return $fillables;
    }

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

    public function getTextAttribute()
    {
        if (! array_key_exists('text', $this->attributes)) {
            $notifynderParse = new NotificationParser();
            $this->attributes['text'] = $notifynderParse->parse($this);
        }

        return $this->attributes['text'];
    }

    public function read()
    {
        $this->update(['read' => 1]);
    }

    public function unread()
    {
        $this->update(['read' => 0]);
    }

    public function resend()
    {
        $this->updateTimestamps();
        $this->read = 0;

        return $this->save();
    }
}
