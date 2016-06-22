<?php

use Fenos\Notifynder\Builder\Builder;
use Fenos\Notifynder\Builder\Notification as BuilderNotification;
use Fenos\Notifynder\Models\Notification as ModelNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Fenos\Notifynder\Managers\SenderManager;

class NotifynderManagerTest extends NotifynderTestCase
{
    public function testCallUndefinedMethod()
    {
        $this->setExpectedException(BadMethodCallException::class);

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
        $sent = $manager->category(1)
            ->from(1)
            ->to(2)
            ->send();

        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
    }

    public function testSendMultipleNotifications()
    {
        $datas = [2, 3, 4];
        $manager = app('notifynder');
        $sent = $manager->loop($datas, function ($builder, $data) {
            $builder->category(1)
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
        $sent = $manager->category(1)
            ->from(1)
            ->to(2)
            ->sendSingle();

        $this->assertTrue($sent);

        $notifications = ModelNotification::all();
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(EloquentCollection::class, $notifications);
    }
}
