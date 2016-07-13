<?php

namespace Fenos\Notifynder\Facades;

use Illuminate\Support\Facades\Facade;

class Notifynder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'notifynder';
    }
}