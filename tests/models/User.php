<?php
namespace Fenos\Tests\Models;

use Fenos\Notifynder\Traits\Notifable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Notifable;

    protected $fillable = ['id'];
}