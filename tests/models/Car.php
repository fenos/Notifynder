<?php

namespace Fenos\Tests\Models;

use Fenos\Notifynder\Traits\Notifable;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use Notifable;

    protected $table = 'cars';

    protected $fillable = [
        'id',
        'brand',
        'model',
    ];
}
