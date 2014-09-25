<?php

use Fenos\Notifynder\Senders\Queue\NotifynderQueue;
use Mockery as m;

/**
 * Class NotifynderQueueTest
 */
class NotifynderQueueTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var NotifynderQueue
     */
    protected $notifynderQueue;

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Illuminate\Queue\QueueManager
     */
    protected $queue;

    /**
     * SetUp UnitTest
     */
    public function setUp()
    {
        $this->notifynderQueue = new NotifynderQueue(
            $this->config = m::mock('Illuminate\Config\Repository'),
            $this->queue = m::mock('Illuminate\Queue\QueueManager')
        );
    }

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_push_a_notification_in_a_queue()
    {
        $info = NotificationDataBuilder::singleNotificationData();

        $this->queue->shouldReceive('push')
             ->once()
             ->with('Fenos\Notifynder\Senders\Queue\QueueSender',$info)
             ->andReturn(true);

        $result = $this->notifynderQueue->push($info);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_check_if_the_queue_is_active_in_the_configuration()
    {
        $this->config->shouldReceive('get')
             ->once()
             ->with('notifynder::config.queue')
             ->andReturn(true);

        $result = $this->notifynderQueue->isActive();

        $this->assertTrue($result);
    }
}
 