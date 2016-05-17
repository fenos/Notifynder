<?php

namespace Fenos\Tests\Models;

use Fenos\Notifynder\Notifable;

class User extends \Illuminate\Database\Eloquent\Model
{
    // Never do this
    protected $fillable = ['id'];
    use Notifable;
}
