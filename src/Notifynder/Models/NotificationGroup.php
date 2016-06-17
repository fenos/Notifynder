<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationGroup extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    public function categories()
    {
        return $this->belongsToMany(
            NotificationCategory::class,
            'notifications_categories_in_groups',
            'group_id', 'category_id'
        );
    }
}
