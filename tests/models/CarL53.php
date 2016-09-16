<?php

namespace Fenos\Tests\Models;

use Fenos\Notifynder\Traits\NotifableLaravel53;
use Illuminate\Database\Eloquent\Model;

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
