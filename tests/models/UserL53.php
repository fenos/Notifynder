<?php

namespace Fenos\Tests\Models;

use Fenos\Notifynder\Traits\NotifableLaravel53;
use Illuminate\Database\Eloquent\Model;

class UserL53 extends Model
{
    use NotifableLaravel53;

    protected $table = 'users';

    protected $fillable = [
        'id',
        'firstname',
        'lastname',
    ];
}
