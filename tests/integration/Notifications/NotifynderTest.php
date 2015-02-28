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
        $this->createCategory(['name' => 'custom']);

        $this->notifynder->extend('sendCustom', 'CustomSender');

        $notifications = $this->notifynder->builder()
                                ->category('custom')
                                ->url('w')
                                ->from(1)
                                ->to(1)
                                ->toArray();

        $this->notifynder->sendCustom($notifications);
    }
}