<?php

use Fenos\Notifynder\Senders\SendMultiple;
use Mockery as m;

class SendMultipleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SendMultiple
     */
    protected $sendMultiple;

    /**
     * @var array
     */
    protected $dataNotification;

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        $this->sendMultiple = new SendMultiple(
            $this->dataNotification = NotificationDataBuilder::multipleNotificationData()
        );
    }

    /**
     * TearDown.
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_send_multiple_notification_stroing_it_in_the_db()
    {
        $storeNotification = m::mock('Fenos\Notifynder\Senders\StoreNotification');

        $storeNotification->shouldReceive('sendMultiple')
            ->once()
            ->with($this->dataNotification)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->sendMultiple->send($storeNotification);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }
}
