<?php

namespace Fenos\Notifynder\Models;

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

    protected $casts = [
        'extra' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        $this->fillable($this->mergeFillables());

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
}
