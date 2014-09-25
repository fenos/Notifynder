<?php

use Laracasts\TestDummy\Factory as TestDummy;

class NotifynderHandlerIntegrationTest extends IntegrationDBTest {

    /**
     * @var \Fenos\Notifynder\Notifynder
     */
    protected $notifynder;

    public function setUp()
    {
        parent::setUp();

        $this->notifynder = $this->app->make('notifynder');

        $this->notifynder->bootListeners();
    }

    /** @test */
    public function it_fire_a_event_storing_2_notifications()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['name' => 'test']);

        $notificationData[0] = 1;
        $notificationData[1] = 2;

        $this->notifynder->fire('test.notifynder.listener','test',$notificationData);

        $notifications = $this->notifynder->getAll(1);

        $this->assertCount(2,$notifications);
    }

    /** @test */
    public function it_fire_a_event_storing_a_single_notification()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['name' => 'test']);

        $notificationData[0] = 1;

        $this->notifynder->fire('test.notifynder.listener','test',$notificationData);

        $notifications = $this->notifynder->getAll(1);

        $this->assertCount(1,$notifications);
    }

    /** @test */
    public function it_delegate_events_to_categories()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['name' => 'test']);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['name' => 'test2']);

        TestDummy::create('DummyModels\User');

        $notificationData[0] = 1;

        $this->notifynder->delegate($notificationData,[
            'test' => 'test.notifynder.listener',
            'test2' => 'test.delegation.listener'
        ]);

        $notifications = $this->notifynder->getAll(1);

        $this->assertCount(2,$notifications);
        $this->assertEquals('test',$notifications[0]->body->name);
        $this->assertEquals('test2',$notifications[1]->body->name);
    }
} 