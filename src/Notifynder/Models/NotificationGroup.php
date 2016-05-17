<?php

namespace Fenos\Notifynder\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationGroup.
 */
class NotificationGroup extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(
            'Fenos\Notifynder\Models\NotificationCategory',
            'notifications_categories_in_groups',
            'group_id', 'category_id'
        );
    }
}
