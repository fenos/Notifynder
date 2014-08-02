<?php

use Fenos\Notifynder\Handler\NotifynderDispatcher;
use Fenos\Notifynder\Notifynder;

class ListenerDummyTest extends NotifynderDispatcher {

    public function whenNotifynderListener($values, $category_name,Notifynder $notifynder)
    {
        return $values;
    }

    public function whenDelegationListener($values, $category_name,Notifynder $notifynder)
    {
        return $values;
    }
} 