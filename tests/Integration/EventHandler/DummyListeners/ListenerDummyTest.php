<?php

use Fenos\Notifynder\Handler\NotifynderDispatcher;
use Fenos\Notifynder\Notifynder;

class ListenerDummyTest extends NotifynderDispatcher {

    public function notifynderListener($values, $category_name,Notifynder $notifynder)
    {
        return $values;
    }

    public function delegationListener($values, $category_name,Notifynder $notifynder)
    {
        return $values;
    }
} 