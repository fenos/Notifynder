<?php

use Fenos\Notifynder\Builder\Builder;
use Fenos\Notifynder\Builder\Notification;
use Fenos\Notifynder\Managers\NotifynderManager;

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
        $category = $this->createCategory();
        $user = $this->createUser();
        $notifynder = $user->sendNotificationFrom($category->getKey());
        $this->assertInstanceOf(NotifynderManager::class, $notifynder);
        $notifynder->to(2);
        $builder = $notifynder->builder();
        $this->assertInstanceOf(Builder::class, $builder);
        $notification = $builder->getNotification();
        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame($category->getKey(), $notification->category_id);
        $this->assertSame($user->getKey(), $notification->from_id);
    }

    public function testSendNotificationTo()
    {
        $category = $this->createCategory();
        $user = $this->createUser();
        $notifynder = $user->sendNotificationTo($category->getKey());
        $this->assertInstanceOf(NotifynderManager::class, $notifynder);
        $notifynder->from(2);
        $builder = $notifynder->builder();
        $this->assertInstanceOf(Builder::class, $builder);
        $notification = $builder->getNotification();
        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame($category->getKey(), $notification->category_id);
        $this->assertSame($user->getKey(), $notification->to_id);
        $notifynder->send();
        $this->assertCount(1, $user->getNotificationRelation);
    }

    public function testNotificationsHasMany()
    {
        $category = $this->createCategory();
        $user = $this->createUser();
        $user
            ->sendNotificationTo($category->getKey())
            ->from(2)
            ->send();
        $this->assertCount(1, $user->getNotificationRelation);
    }

    public function testNotificationsMorphMany()
    {
        notifynder_config()->set('polymorphic', true);

        $user = $this->createUser();
        $this->sendNotificationTo($user);
        $car = $this->createCar();
        $this->sendNotificationTo($car);
        $this->assertCount(1, $user->getNotificationRelation);
        $this->assertCount(1, $car->getNotificationRelation);
    }

    public function testGetNotificationsDefault()
    {
        $user = $this->createUser();
        $this->sendNotificationsTo($user, 25);
        $this->assertCount(25, $user->getNotifications());
    }

    public function testGetNotificationsLimited()
    {
        $user = $this->createUser();
        $this->sendNotificationsTo($user, 25);
        $this->assertCount(10, $user->getNotifications(10));
    }

    public function testGetNotificationsNotRead()
    {
        $user = $this->createUser();
        $this->sendNotificationsTo($user, 25);
        $this->assertSame(25, $user->readAllNotifications());
        $this->assertCount(0, $user->getNotificationsNotRead());
    }

    public function testGetNotificationsNotReadLimited()
    {
        $user = $this->createUser();
        $this->sendNotificationsTo($user, 25);
        $this->assertCount(10, $user->getNotificationsNotRead(10));
    }

    public function testReadStatusRelatedMethods()
    {
        $user = $this->createUser();
        $this->sendNotificationsTo($user, 25);
        $this->assertSame(25, $user->countUnreadNotifications());
        $this->assertSame(25, $user->readAllNotifications());
        $this->assertSame(0, $user->countUnreadNotifications());
        $this->assertSame(25, $user->unreadAllNotifications());
        $this->assertSame(25, $user->countUnreadNotifications());
        $notification = $user->getNotificationRelation->first();
        $this->assertTrue($user->readNotification($notification));
        $this->assertSame(24, $user->countUnreadNotifications());
        $this->assertTrue($user->unreadNotification($notification->getKey()));
        $this->assertSame(25, $user->countUnreadNotifications());

        $user2 = $this->createUser();
        $this->sendNotificationsTo($user2, 5);
        $this->assertFalse($user->readNotification($user2->getNotificationRelation->first()));
    }
}
