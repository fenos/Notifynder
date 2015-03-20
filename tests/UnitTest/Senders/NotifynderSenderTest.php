<?php
/**
 * Created by Fabrizio Fenoglio.
 */
use Fenos\Notifynder\Senders\NotifynderSender;
use Mockery as m;

/**
 * Class NotifynderSenderTest
 */
class NotifynderSenderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var NotifynderSender
     */
    protected $notifynderSender;

    /**
     * @var \Fenos\Notifynder\Senders\NotifynderSenderFactory
     */
    protected $senderFactory;

    /**
     * @var \Fenos\Notifynder\Senders\NotificationRepository
     */
    protected $notification;

    /**
     * @var \Fenos\Notifynder\Senders\Queue\NotifynderQueue
     */
    protected $notifynderQueue;

    /**
     * @var array
     */
    protected $dependency = [];

    /**
     * Set Up Test
     */
    public function setUp()
    {
        $model = m::mock('Illuminate\Database\Eloquent\Model');

        $this->notifynderSender = new NotifynderSender(
            $this->senderFactory = m::mock('Fenos\Notifynder\Senders\NotifynderSenderFactory'),
            $this->notification = m::mock('Fenos\Notifynder\Notifications\NotifynderNotification'),
            $this->notifynderQueue = m::mock('Fenos\Notifynder\Senders\Queue\NotifynderQueue')
        );

        $this->setDependency();
    }

    /**
     * TearDown
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_send_single_notification_using_the_right_sender()
    {
        $notificationSenderMock = m::mock('Fenos\Notifynder\Senders\NotifynderSender[getQueue,sendNow]', $this->dependency);

        $this->notifynderQueue->shouldReceive('isActive')
            ->once()
            ->andReturn(false);

        $infoNotifications = NotificationDataBuilder::singleNotificationData();

        $notificationSenderMock->shouldReceive('sendNow')
             ->once()
             ->with($infoNotifications, null)
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $notificationSenderMock->send($infoNotifications);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_send_a_notification_processed_by_queue()
    {
        $notificationSenderMock = m::mock('Fenos\Notifynder\Senders\NotifynderSender[getQueue]', $this->dependency);

        $info = NotificationDataBuilder::singleNotificationData();

        $category = null;

        $this->notifynderQueue->shouldReceive('isActive')
            ->once()
            ->andReturn(true);

        $this->notifynderQueue->shouldReceive('push')
             ->once()
             ->with(['info' => $info, 'category' => $category])
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $notificationSenderMock->send($info, $category);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_test_send_now_notifications()
    {
        $sender = m::mock('Fenos\Notifynder\Senders\SendOne');

        $infoNotifications = NotificationDataBuilder::singleNotificationData();

        $this->senderFactory->shouldReceive('getSender')
             ->once()
             ->with($infoNotifications, null)
             ->andReturn($sender);

        $sender->shouldReceive('send')
             ->once()
             ->with($this->notification)
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynderSender->sendNow($infoNotifications, null);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_send_now_multiple_notification_using_the_right_sender()
    {
        $infoNotifications = NotificationDataBuilder::multipleNotificationData();

        $sender = m::mock('Fenos\Notifynder\Senders\SendMultiple');

        $this->senderFactory->shouldReceive('getSender')
            ->once()
            ->with($infoNotifications, null)
            ->andReturn($sender);

        $sender->shouldReceive('send')
            ->once()
            ->with($this->notification)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynderSender->sendNow($infoNotifications);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_send_one_notification()
    {
        $infoNotifications = NotificationDataBuilder::singleNotificationData();

        $sendSingle = m::mock('Fenos\Notifynder\Senders\SendOne');

        $this->notifynderQueue->shouldReceive('isActive')
            ->once()
            ->andReturn(false);

        $this->senderFactory->shouldReceive('sendSingle')
             ->once()
             ->with($infoNotifications, null)
             ->andReturn($sendSingle);

        $sendSingle->shouldReceive('send')
             ->once()
             ->with($this->notification, null)
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynderSender->sendOne($infoNotifications);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification', $result);
    }

    /** @test */
    public function it_send_multiple_notification()
    {
        $infoNotifications = NotificationDataBuilder::multipleNotificationData();

        $sendMultiple = m::mock('Fenos\Notifynder\Senders\SendMultiple');

        $this->notifynderQueue->shouldReceive('isActive')
            ->once()
            ->andReturn(false);

        $this->senderFactory->shouldReceive('sendMultiple')
            ->once()
            ->with($infoNotifications)
            ->andReturn($sendMultiple);

        $sendMultiple->shouldReceive('send')
            ->once()
            ->with($this->notification)
            ->andReturn(true);

        $result = $this->notifynderSender->sendMultiple($infoNotifications);

        $this->assertTrue($result);
    }

    /**
     * Set Dependency Class
     */
    public function setDependency()
    {
        $this->dependency = [$this->senderFactory,$this->notification,$this->notifynderQueue];
    }
}
