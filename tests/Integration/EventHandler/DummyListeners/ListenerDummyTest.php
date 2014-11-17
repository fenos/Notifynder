<?php

use Carbon\Carbon;
use Fenos\Notifynder\Handler\NotifynderDispatcher;
use Fenos\Notifynder\Notifynder;

class ListenerDummyTest extends NotifynderDispatcher {

    public function notifynderListener($values, $category_name,Notifynder $notifynder)
    {
        $notifications = [];

        $date = Carbon::now();

        foreach($values as $key => $value)
        {
            $notifications[] = [
                'from_id'     => 2,
                'from_type'   => "User",
                'to_id'       => 1,
                'to_type'     => "Team",
                'category_id' => $notifynder->category($category_name)->id(),
                'url'         => 'www.urlofnotification.com',
                'extra'       => "firstNotify{$key}",
                'created_at'  => $date,
                'updated_at'  => $date,
            ];
        }

        return $notifications;
    }

    public function delegationListener($values, $category_name,Notifynder $notifynder)
    {
        $notifications = [];

        $date = Carbon::now();

        foreach($values as $key => $value)
        {
            $notifications[] = [
                'from_id'     => 2,
                'from_type'   => "User",
                'to_id'       => 1,
                'to_type'     => "User",
                'category_id' => $notifynder->category($category_name)->id(),
                'url'         => 'www.urlofnotification.com',
                'extra'       => "same{$key}",
                'created_at'  => $date,
                'updated_at'  => $date,
            ];
        }

        return $notifications;
    }
} 