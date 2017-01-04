<?php

namespace Fenos\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Fenos\Notifynder\Traits\NotifableLaravel53;

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
