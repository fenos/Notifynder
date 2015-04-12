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
            return new CustomSender($notification,$app->make('notifynder'));
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
}