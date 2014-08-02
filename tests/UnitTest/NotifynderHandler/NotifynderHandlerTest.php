<?php

use Fenos\Notifynder\Handler\NotifynderHandler;
use Mockery as m;

/**
 * Class NotifynderHandlerTest
 */
class NotifynderHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var NotifynderHandler
     */
    protected $notifynderHandler;

    /**
     * @var Illuminate\Events\Dispatcher
     */
    protected $event;

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    public function setUp()
    {
        $this->notifynderHandler = new NotifynderHandler(
            $this->event = m::mock('Illuminate\Events\Dispatcher'),
            $this->config = m::mock('Illuminate\Config\Repository')
        );
    }

    /**
     * Tear down function for all tests
     *
     */
    public function teardown()
    {
        m::close();
    }

    /** @test */
    public function it_fire_an_event_registered()
    {
        $mockNotifynder = m::mock('Fenos\Notifynder\Notifynder');

        $keyEvent = "user.post.add";
        $category_name = "category";
        $eventName = ['eventName' => $keyEvent];

        $multipleNotificationData = NotificationDataBuilder::multipleNotificationData();

        $this->event->shouldReceive('fire')
             ->once()
             ->with($keyEvent,[$eventName,$category_name,$mockNotifynder])
             ->andReturn($multipleNotificationData);

        $mockNotifynder->shouldReceive('send')
             ->once()
             ->with($multipleNotificationData[0])
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynderHandler->fire($mockNotifynder,$keyEvent,$category_name);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_delegate_categories_to_events_registered()
    {
        $mockNotifynder = m::mock('Fenos\Notifynder\Notifynder');

        $delegation = [
            'category.name' => 'event.key.name'
        ];

        $multipleNotificationData = NotificationDataBuilder::multipleNotificationData(1);

        $eventData = $multipleNotificationData;
        $eventData['eventName'] = 'event.key.name';

        $fetchNotification = [[$multipleNotificationData]];

        $this->event->shouldReceive('fire')
            ->once()
            ->with($eventData['eventName'],[$eventData,'category.name',$mockNotifynder])
            ->andReturn($fetchNotification);

        $mockNotifynder->shouldReceive('send')
            ->once()
            ->with($fetchNotification[0])
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynderHandler->delegate($mockNotifynder,$multipleNotificationData,$delegation);

        $this->assertNull($result);
    }
}
 