<?php

use Carbon\Carbon;
use Fenos\Notifynder\Builder\Builder;
use Fenos\Notifynder\Builder\Notification;
use Fenos\Notifynder\Exceptions\UnvalidNotificationException;

class BuilderTest extends NotifynderTestCase
{
    public function testCreateSingleNotification()
    {
        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);

        $this->assertSame(1, $notification->category_id);
        $this->assertSame(1, $notification->from_id);
        $this->assertSame('Fenos\Tests\Models\User', $notification->from_type);
        $this->assertSame(2, $notification->to_id);
        $this->assertSame('Fenos\Tests\Models\User', $notification->to_type);
        $this->assertInstanceOf(Carbon::class, $notification->created_at);
        $this->assertInstanceOf(Carbon::class, $notification->updated_at);
    }

    public function testCreateSingleNotificationWithAll()
    {
        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->url('https://google.com')
            ->extra([
                'foo' => 'bar',
            ])
            ->expire(Carbon::tomorrow())
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);

        $this->assertSame('https://google.com', $notification->url);
        $this->assertInternalType('array', $notification->extra);
        $this->assertCount(1, $notification->extra);
        $this->assertSame('bar', $notification->extra['foo']);
        $this->assertInstanceOf(Carbon::class, $notification->expire_time);
    }

    public function testCreateSingleNotificationAndGetArray()
    {
        $builder = new Builder();
        $notifications = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->getNotifications();

        $this->assertInternalType('array', $notifications);
        $this->assertCount(1, $notifications);

        $this->assertInstanceOf(Notification::class, $notifications[0]);
    }

    public function testCreateSingleUnvalidNotification()
    {
        $this->setExpectedException(UnvalidNotificationException::class);

        $builder = new Builder();
        $builder
            ->from(1)
            ->to(2)
            ->getNotification();
    }

    public function testCreateMultipleNotifications()
    {
        $datas = [2,3,4];
        $builder = new Builder();
        $notifications = $builder->loop($datas, function($builder, $data) {
            $builder->category(1)
                ->from(1)
                ->to($data);
        })->getNotifications();

        $this->assertInternalType('array', $notifications);
        $this->assertCount(count($datas), $notifications);

        foreach($notifications as $index => $notification) {
            $this->assertInstanceOf(Notification::class, $notification);

            $this->assertSame(1, $notification->category_id);
            $this->assertSame(1, $notification->from_id);
            $this->assertSame('Fenos\Tests\Models\User', $notification->from_type);
            $this->assertSame($datas[$index], $notification->to_id);
            $this->assertSame('Fenos\Tests\Models\User', $notification->to_type);
            $this->assertInstanceOf(Carbon::class, $notification->created_at);
            $this->assertInstanceOf(Carbon::class, $notification->updated_at);
        }
    }

    public function testCreateMultipleUnvalidNotifications()
    {
        $this->setExpectedException(UnvalidNotificationException::class);

        $builder = new Builder();
        $builder->loop([2,3,4], function($builder, $data) {
            $builder->category(1)
                ->to($data);
        })->getNotifications();
    }
}