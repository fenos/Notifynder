<?php

namespace Fenos\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Fenos\Notifynder\Traits\NotifableLaravel53;

class CarL53 extends Model
{
    use NotifableLaravel53;

    protected $table = 'cars';

    protected $fillable = [
        'id',
        'brand',
        'model',
    ];
}
