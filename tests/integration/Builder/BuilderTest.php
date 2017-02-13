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

    public function testCreateSingleAnonymousNotification()
    {
        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->anonymous()
            ->to(2)
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);

        $this->assertSame(1, $notification->category_id);
        $this->assertNull($notification->from_id);
        $this->assertNull($notification->from_type);
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
            ->url('http://notifynder.info')
            ->extra([
                'foo' => 'bar',
            ])
            ->expire(Carbon::tomorrow())
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);

        $this->assertSame('http://notifynder.info', $notification->url);
        $this->assertInternalType('array', $notification->extra);
        $this->assertCount(1, $notification->extra);
        $this->assertSame('bar', $notification->extra['foo']);
        $this->assertInstanceOf(Carbon::class, $notification->expires_at);
    }

    public function testCreateSingleNotificationWithExtendedExtra()
    {
        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->extra([
                'foo' => 'bar',
            ], false)
            ->extra([
                'hello' => 'world',
            ], false)
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);

        $this->assertInternalType('array', $notification->extra);
        $this->assertCount(2, $notification->extra);
        $this->assertSame('bar', $notification->extra['foo']);
        $this->assertSame('world', $notification->extra['hello']);
    }

    public function testCreateSingleNotificationWithOverriddenExtra()
    {
        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->extra([
                'foo' => 'bar',
            ], true)
            ->extra([
                'hello' => 'world',
            ], true)
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);

        $this->assertInternalType('array', $notification->extra);
        $this->assertCount(1, $notification->extra);
        $this->assertSame('world', $notification->extra['hello']);
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
        $this->expectException(UnvalidNotificationException::class);

        $builder = new Builder();
        $builder
            ->from(1)
            ->to(2)
            ->getNotification();
    }

    public function testCreateSingleCatchedUnvalidNotificationW()
    {
        try {
            $builder = new Builder();
            $builder
                ->from(1)
                ->to(2)
                ->getNotification();
        } catch (UnvalidNotificationException $e) {
            $this->assertInstanceOf(Notification::class, $e->getNotification());
        }
    }

    public function testCreateMultipleNotifications()
    {
        $datas = [2, 3, 4];
        $builder = new Builder();
        $notifications = $builder->loop($datas, function ($builder, $data) {
            $builder->category(1)
                ->from(1)
                ->to($data);
        })->getNotifications();

        $this->assertInternalType('array', $notifications);
        $this->assertCount(count($datas), $notifications);

        foreach ($notifications as $index => $notification) {
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
        $this->expectException(UnvalidNotificationException::class);

        $builder = new Builder();
        $builder->loop([2, 3, 4], function ($builder, $data) {
            $builder->category(1)
                ->to($data);
        })->getNotifications();
    }

    public function testCreateSingleNotificationWithAdditionalField()
    {
        notifynder_config()->set('additional_fields.fillable', []);

        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->setField('additional_field', 'value')
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame(1, $notification->category_id);
        $this->assertNull($notification->additional_field);

        notifynder_config()->set('additional_fields.fillable', ['additional_field']);

        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->setField('additional_field', 'value')
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame(1, $notification->category_id);
        $this->assertSame('value', $notification->additional_field);
    }

    public function testCreateSingleUnvalidNotificationWithRequiredField()
    {
        $this->expectException(UnvalidNotificationException::class);

        notifynder_config()->set('additional_fields.required', ['required_field']);

        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->getNotification();
    }

    public function testCreateSingleNotificationWithRequiredField()
    {
        notifynder_config()->set('additional_fields.required', ['required_field']);

        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(1)
            ->to(2)
            ->setField('required_field', 'value')
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame(1, $notification->category_id);
        $this->assertSame('value', $notification->required_field);
    }

    public function testCreateSingleNotificationWithSplittedEntityData()
    {
        $builder = new Builder();
        $notification = $builder
            ->category(1)
            ->from(\Fenos\Tests\Models\User::class, 1)
            ->to(\Fenos\Tests\Models\User::class, 2)
            ->getNotification();

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame(1, $notification->category_id);
        $this->assertSame(1, $notification->from_id);
        $this->assertSame(\Fenos\Tests\Models\User::class, $notification->from_type);
        $this->assertSame(2, $notification->to_id);
        $this->assertSame(\Fenos\Tests\Models\User::class, $notification->to_type);
    }

    public function testOffsetMethods()
    {
        $builder = new Builder();
        $builder->offsetSet('foo', 'bar');
        $this->assertTrue($builder->offsetExists('foo'));
        $this->assertSame('bar', $builder->offsetGet('foo'));
        $builder->offsetUnset('foo');
        $this->assertFalse($builder->offsetExists('foo'));
    }
}
