<?php

use Fenos\Notifynder\Builder\Builder;
use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Managers\SenderManager;
use Fenos\Notifynder\Models\Notification as ModelNotification;
use Fenos\Notifynder\Builder\Notification as BuilderNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class NotifynderManagerTest extends NotifynderTestCase
{
    public function testCallUndefinedMethod()
    {
        $this->expectException(BadMethodCallException::class);

        $manager = app('notifynder');
        $manager->undefinedMethod();
    }

    public function testGetBuilderInstance()
    {
        $manager = app('notifynder');
        $builder = $manager->builder();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    public function testGetSenderInstance()
    {
        $manager = app('notifynder');
        $sender = $manager->sender();

        $this->assertInstanceOf(SenderManager::class, $sender);
    }

    public function testBuildSingleNotification()
    {
        $manager = app('notifynder');
        $notification = $manager->category(1)
            ->from(1)
            ->to(2)
            ->getNotification();

        $this->assertInstanceOf(BuilderNotification::class, $notification);
    }

    public function testBuildMultipleNotifications()
    {
        $datas = [2, 3, 4];
        $manager = app('notifynder');
        $notifications = $manager->loop($datas, function ($builder, $data) {
            $builder->category(1)
                ->from(1)
                ->to($data);
        })->getNotifications();

        $this->assertInternalType('array', $notifications);
        $this->assertCount(count($datas), $notifications);
    }

    public function testSendSingleNotification()
    {
        $manager = app('notifynder');
        $category = $this->createCategory();
        $sent = $manager->category($category->getKey())
            ->from(1)
            ->to(2)
            ->send();

        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
    }

    public function testSendSingleAnonymousNotification()
    {
        $manager = app('notifynder');
        $category = $this->createCategory();
        $sent = $manager->category($category->getKey())
            ->anonymous()
            ->to(2)
            ->send();

        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
        $notification = $notifications->first();
        $this->assertInstanceOf(ModelNotification::class, $notification);
        $this->assertNull($notification->from);
        $this->assertNull($notification->from_id);
        $this->assertNull($notification->from_type);
        $this->assertTrue($notification->isAnonymous());
    }

    public function testSendMultipleNotifications()
    {
        $datas = [2, 3, 4];
        $manager = app('notifynder');
        $category = $this->createCategory();
        $sent = $manager->loop($datas, function ($builder, $data) use ($category) {
            $builder->category($category->getKey())
                ->from(1)
                ->to($data);
        })->send();

        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(count($datas), $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
    }

    public function testSendSingleSpecificNotification()
    {
        $manager = app('notifynder');
        $category = $this->createCategory();
        $sent = $manager->category($category->getKey())
            ->from(1)
            ->to(2)
            ->sendSingle();

        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
    }

    public function testSendOnceSameNotifications()
    {
        $manager = app('notifynder');
        $category = $this->createCategory();
        $sent = $manager->category($category->getKey())
            ->from(1)
            ->to(2)
            ->extra(['foo' => 'bar'])
            ->sendOnce();
        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
        $notificationFirst = $notifications->first();
        $this->assertInstanceOf(Notification::class, $notificationFirst);

        $this->assertEquals(0, $notificationFirst->read);
        $notificationFirst->read();
        $this->assertEquals(1, $notificationFirst->read);

        sleep(1);

        $sent = $manager->category($category->getKey())
            ->from(1)
            ->to(2)
            ->extra(['foo' => 'bar'])
            ->sendOnce();
        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
        $notificationSecond = $notifications->first();
        $this->assertInstanceOf(Notification::class, $notificationSecond);

        $this->assertEquals(0, $notificationSecond->read);

        $this->assertSame($notificationFirst->getKey(), $notificationSecond->getKey());
        $this->assertEquals($notificationFirst->created_at, $notificationSecond->created_at);
        $diff = $notificationFirst->updated_at->diffInSeconds($notificationSecond->updated_at);
        $this->assertGreaterThan(0, $diff);
    }

    public function testSendOnceDifferentNotifications()
    {
        $manager = app('notifynder');
        $category = $this->createCategory();
        $sent = $manager->category($category->getKey())
            ->from(1)
            ->to(2)
            ->extra(['foo' => 'bar'])
            ->sendOnce();
        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);

        $sent = $manager->category($category->getKey())
            ->from(2)
            ->to(1)
            ->extra(['hello' => 'world'])
            ->sendOnce();
        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(2, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
    }
}
