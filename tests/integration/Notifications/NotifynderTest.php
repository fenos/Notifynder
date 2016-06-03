<?php

use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\NotifynderManager;
use Fenos\Tests\Models\User;
use Laracasts\TestDummy\Factory;

/**
 * Class NotifynderTest.
 */
class NotifynderTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var NotifynderManager
     */
    protected $notifynder;

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        parent::setUp();
        $this->notifynder = app('notifynder');
    }

    /** @test */
    public function it_call_an_extended_method()
    {
        $this->createCategory(['name' => 'customs']);

        $this->notifynder->extend('sendCustom', function ($notification, $app) {
            return new CustomDefaultSender($notification, $app->make('notifynder'));
        });

        $notifications = $this->notifynder
                                ->category('customs')
                                ->url('w')
                                ->from(1)
                                ->to(1)
                                ->sendCustom();

        $this->assertEquals('w', $notifications->url);
    }

    /** @test */
    public function it_send_a_notification_with_the_new_way()
    {
        $this->createCategory(['name' => 'custom']);

        $notifications = $this->notifynder
            ->category('custom')
            ->url('w')
            ->from(1)
            ->to(1);

        $notifications = $this->notifynder->send($notifications);
        $this->assertEquals('w', $notifications->url);
    }

    /** @test */
    public function it_send_using_notifynder_as_an_array()
    {
        $this->createCategory(['name' => 'custom']);

        $this->notifynder['category'] = 'custom';
        $this->notifynder['url'] = 'w';
        $this->notifynder['from'] = 1;
        $this->notifynder['to'] = 1;
        $notification = $this->notifynder->send();

        $this->assertEquals('w', $notification->url);
    }

    /** @test */
    public function it_send_using_notifynder_as_an_object()
    {
        $this->createCategory(['name' => 'custom']);

        $notifynder = $this->notifynder;
        $notifynder->category = 'custom';
        $notifynder->url = 'w';
        $notifynder->from = 1;
        $notifynder->to = 1;
        $notification = $notifynder->send();

        $this->assertEquals('w', $notification->url);
    }

    /**
     * @test
     */
    public function it_store_extra_field_as_json()
    {
        $this->createCategory(['name' => 'custom']);

        $extra = ['extra.name' => 'amazing'];

        $notifications = $this->notifynder
            ->category('custom')
            ->extra($extra)
            ->url('w')
            ->from(1)
            ->to(1);

        $notifications = $this->notifynder->send($notifications);
        $this->assertEquals($notifications->extra->toArray(), $extra);
    }

    /**
     * It send multiple Notifications.
     *
     * @method send
     * @group failing
     * @test
     */
    public function it_send_multiple_notifications()
    {
        Factory::times(10)->create(User::class);
        $this->createCategory(['name' => 'me']);

        $allUsers = User::all();

        $this->notifynder->loop($allUsers, function ($builder, $user) {
            $builder->category('me')
                ->url('you')
                ->from(1)
                ->to($user->id);
        })->send();

        // should send 10 notifications
        $notifications = Notification::all();

        $this->assertCount(10, $notifications);
    }
}
