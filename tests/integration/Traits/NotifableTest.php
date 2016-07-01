<?php

use Fenos\Notifynder\Managers\NotifynderManager;
use Fenos\Notifynder\Builder\Builder;
use Fenos\Notifynder\Builder\Notification;

class NotifableTest extends NotifynderTestCase
{
    public function testNotifynder()
    {
        $user = $this->createUser();
        $notifynder = $user->notifynder(1);
        $this->assertInstanceOf(NotifynderManager::class, $notifynder);
        $notifynder->from(1)->to(2);
        $builder = $notifynder->builder();
        $this->assertInstanceOf(Builder::class, $builder);
        $notification = $builder->getNotification();
        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame(1, $notification->category_id);
    }

    public function testSendNotificationFrom()
    {
        $user = $this->createUser();
        $notifynder = $user->sendNotificationFrom(1);
        $this->assertInstanceOf(NotifynderManager::class, $notifynder);
        $notifynder->to(2);
        $builder = $notifynder->builder();
        $this->assertInstanceOf(Builder::class, $builder);
        $notification = $builder->getNotification();
        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame(1, $notification->category_id);
        $this->assertSame(1, $notification->from_id);
    }

    public function testSendNotificationTo()
    {
        $user = $this->createUser();
        $notifynder = $user->sendNotificationTo(1);
        $this->assertInstanceOf(NotifynderManager::class, $notifynder);
        $notifynder->from(2);
        $builder = $notifynder->builder();
        $this->assertInstanceOf(Builder::class, $builder);
        $notification = $builder->getNotification();
        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame(1, $notification->category_id);
        $this->assertSame(1, $notification->to_id);
        $notifynder->send();
        $this->assertCount(1, $user->notifications);
    }
}
