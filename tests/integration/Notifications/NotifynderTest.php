<?php
use Fenos\Notifynder\NotifynderManager;

/**
 * Class NotifynderTest
 */
class NotifynderTest extends TestCaseDB {

    use CreateModels;

    /**
     * @var NotifynderManager
     */
    protected $notifynder;

    /**
     * Set Up Test
     */
    public function setUp()
    {
        parent::setUp();
        $this->notifynder = app('notifynder');
    }

    /** @test */
    function it_call_an_extended_method()
    {
        $this->createCategory(['name' => 'customs']);

        $this->notifynder->extend('sendCustom', function($notification,$app) {
            return new CustomDefaultSender($notification,$app->make('notifynder'));
        });

        $notifications = $this->notifynder
                                ->category('customs')
                                ->url('w')
                                ->from(1)
                                ->to(1)
                                ->sendCustom();

        $this->assertEquals('w',$notifications->url);
    }

    /** @test */
    function it_send_a_notification_with_the_new_way()
    {
        $this->createCategory(['name' => 'custom']);

        $notifications = $this->notifynder
            ->category('custom')
            ->url('w')
            ->from(1)
            ->to(1);

        $notifications = $this->notifynder->send($notifications);
        $this->assertEquals('w',$notifications->url);
    }


    /** @test */
    function it_send_using_notifynder_as_an_array()
    {
        $this->createCategory(['name' => 'custom']);

        $this->notifynder['category_id'] = 'custom';
        $this->notifynder['url'] = 'w';
        $this->notifynder['from_id'] = 1;
        $this->notifynder['to_id'] = 1;
        $notification = $this->notifynder->send();

        $this->assertEquals('w',$notification->url);
    }

    /** @test */
    function it_send_using_notifynder_as_an_object()
    {
        $this->createCategory(['name' => 'custom']);

        $notifynder = $this->notifynder;
        $notifynder->category_id = 'custom';
        $notifynder->url = 'w';
        $notifynder->from_id = 1;
        $notifynder->to_id = 1;
        $notification = $notifynder->send();

        $this->assertEquals('w',$notification->url);
    }
}