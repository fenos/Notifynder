<?php

use Fenos\Notifynder\Notifications\NotifynderNotification;
use Mockery as m;

/**
 * Class NotifynderNotificationTest
 *
 * @package Notifications
 */
class NotifynderNotificationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Fenos\Notifynder\Notifications\Repositories\NotificationRepository
     */
    protected $notificationRepo;

    /**
     * @var NotifynderNotification
     */
    protected $notifynderNotification;

    /**
     * @var \Fenos\Notifynder\Models\Notification
     */
    protected $notificationModel;

    /**
     * @var array
     */
    protected $dependency = [];

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->notifynderNotification = new NotifynderNotification(
            $this->notificationRepo = m::mock('Fenos\Notifynder\Notifications\Repositories\NotificationRepository')
        );

        $this->notificationModel = m::mock('Fenos\Notifynder\Models\Notification');

        $this->dependency = $this->setDependency();
    }

    /**
     * TearDown
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_find_a_notification_by_id()
    {
        $notification_id = 1;

        $this->notificationRepo->shouldReceive('find')
             ->once()
             ->with($notification_id)
             ->andReturn($this->notificationModel);

        $result = $this->notifynderNotification->find($notification_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\NotificationNotFoundException
     * */
    public function it_find_a_notification_by_id_but_it_doesn_exists()
    {
        $notification_id = 1;

        $this->notificationRepo->shouldReceive('find')
            ->once()
            ->with($notification_id)
            ->andReturn(null);

        $this->notifynderNotification->find($notification_id);
    }

    /** @test */
    public function it_make_read_a_notification_by_id()
    {
        $notification_id = 1;

        $mockNotifynderNotification = m::mock('Fenos\Notifynder\Notifications\NotifynderNotification[find]', $this->dependency);

        $mockNotifynderNotification->shouldReceive('find')
            ->once()
            ->with($notification_id)
            ->andReturn($this->notificationModel);

        $this->notificationRepo->shouldReceive('readOne')
             ->once()
             ->with($this->notificationModel)
             ->andReturn($this->notificationModel);

        $result = $mockNotifynderNotification->readOne($notification_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_make_read_a_limit_of_notifications_of_the_current_entity()
    {
        $to_id = 1;
        $numbers = 10;
        $orders = "ASC";

        $this->notificationRepo->shouldReceive('entity')
             ->once()
             ->with(null)
             ->andReturn($this->notificationRepo);

        $this->notificationRepo->shouldReceive('readLimit')
            ->once()
            ->with($to_id, $numbers, $orders)
            ->andReturn($this->notificationModel);

        $result = $this->notifynderNotification->readLimit($to_id, $numbers, $orders);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_make_read_all_notifications_of_the_current_entity()
    {
        $to_id = 1;

        $this->notificationRepo->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notificationRepo);

        $this->notificationRepo->shouldReceive('readAll')
            ->once()
            ->with($to_id)
            ->andReturn($this->notificationModel);

        $result = $this->notifynderNotification->readAll($to_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_delete_a_single_notification_by_id()
    {
        $notification_id = 1;

        $this->notificationRepo->shouldReceive('delete')
            ->once()
            ->with($notification_id)
            ->andReturn(true);

        $result = $this->notifynderNotification->delete($notification_id);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_delete_limiting_notifications()
    {
        $to_id = 1;
        $numbers = 10;
        $orders = "ASC";

        $this->notificationRepo->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notificationRepo);

        $this->notificationRepo->shouldReceive('deleteLimit')
            ->once()
            ->with($to_id, $numbers, $orders)
            ->andReturn($this->notificationModel);

        $result = $this->notifynderNotification->deleteLimit($to_id, $numbers, $orders);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_delete_all_notifications_of_the_current_entity()
    {
        $to_id = 1;

        $this->notificationRepo->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notificationRepo);

        $this->notificationRepo->shouldReceive('deleteAll')
            ->once()
            ->with($to_id)
            ->andReturn($this->notificationModel);

        $result = $this->notifynderNotification->deleteAll($to_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_get_not_read_notifications()
    {
        $to_id = 1;
        $limit = 10;
        $paginate = false;

        $this->notificationRepo->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notificationRepo);

        $this->notificationRepo->shouldReceive('getNotRead')
            ->once()
            ->with($to_id, $limit, $paginate)
            ->andReturn($this->notificationModel);

        $result = $this->notifynderNotification->getNotRead($to_id, $limit, $paginate);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_get_all_notifications_of_the_given_entity()
    {
        $to_id = 1;
        $limit = 10;
        $paginate = false;

        $this->notificationRepo->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notificationRepo);

        $this->notificationRepo->shouldReceive('getAll')
            ->once()
            ->with($to_id, $limit, $paginate)
            ->andReturn($this->notificationModel);

        $result = $this->notifynderNotification->getAll($to_id, $limit, $paginate);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_store_a_single_notification()
    {
        $info = NotificationDataBuilder::singleNotificationData();

        $this->notificationRepo->shouldReceive('sendSingle')
             ->once()
             ->with($info)
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynderNotification->sendOne($info);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_store_multiple_notifications()
    {
        $info = NotificationDataBuilder::multipleNotificationData(3);

        $this->notificationRepo->shouldReceive('sendMultiple')
            ->once()
            ->with($info)
            ->andReturn(3);

        $result = $this->notifynderNotification->sendMultiple($info);

        $this->assertEquals(3, $result);
    }

    private function setDependency()
    {
        return [$this->notificationRepo];
    }
}
