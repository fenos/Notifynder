<?php
/**
 * Created by Fabrizio Fenoglio.
 */
use Fenos\Notifynder\Senders\NotifynderSenderFactory;
use Mockery as m;

/**
 * Class NotifynderSenderHandlerTest.
 */
class NotifynderSenderFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var NotifynderSenderFactory
     */
    protected $notifynderSenderFactory;

    /**
     * @var \Fenos\Notifynder\Groups\NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var array
     */
    protected $dependency = [];

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        $this->notifynderSenderFactory = new NotifynderSenderFactory(
            $this->notifynderGroup = m::mock('Fenos\Notifynder\Groups\NotifynderGroup')
        );

        $this->dependency = $this->setDependency();
    }

    /**
     * TearDown.
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_get_the_single_notification_sender_giving_the_array_of_data()
    {
        $infoNotifications = NotificationDataBuilder::singleNotificationData();

        $mockSenderHandler = m::mock('Fenos\Notifynder\Senders\NotifynderSenderFactory[isMultiArray]', $this->dependency);

        $mockSenderHandler->shouldReceive('isMultiArray')
            ->once()
            ->with($infoNotifications)
            ->andReturn(false);

        $result = $mockSenderHandler->getSender($infoNotifications, null);

        $this->assertInstanceOf('Fenos\Notifynder\Senders\SendOne', $result);
    }

    /** @test */
    public function it_get_the_multiple_notification_sender_giving_the_array_of_data()
    {
        $infoNotifications = NotificationDataBuilder::multipleNotificationData();

        $mockSenderHandler = m::mock('Fenos\Notifynder\Senders\NotifynderSenderFactory[isMultiArray]', $this->dependency);

        $mockSenderHandler->shouldReceive('isMultiArray')
            ->once()
            ->with($infoNotifications)
            ->andReturn(true);

        $result = $mockSenderHandler->getSender($infoNotifications);

        $this->assertInstanceOf('Fenos\Notifynder\Senders\SendMultiple', $result);
    }

    /** @test */
    public function it_instantiace_the_sender_of_single_notification()
    {
        $info = NotificationDataBuilder::singleNotificationData();

        $category = 'User';

        $result = $this->notifynderSenderFactory->sendSingle($info, $category);

        $this->assertInstanceOf('Fenos\Notifynder\Senders\SendOne', $result);
    }

    /** @test */
    public function it_instantiace_the_sender_of_multiple_notifications()
    {
        $info = NotificationDataBuilder::multipleNotificationData();

        $category = null;

        $result = $this->notifynderSenderFactory->sendMultiple($info, $category);

        $this->assertInstanceOf('Fenos\Notifynder\Senders\SendMultiple', $result);
    }

    /** @test */
    public function it_test_if_an_multidimesional_array_is_multidimensional()
    {
        $oneLevelArray = NotificationDataBuilder::multipleNotificationData();

        $result = $this->notifynderSenderFactory->isMultiArray($oneLevelArray);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_test_if_an_single_level_array_is_multidimensional()
    {
        $oneLevelArray = NotificationDataBuilder::singleNotificationData();

        $result = $this->notifynderSenderFactory->isMultiArray($oneLevelArray);

        $this->assertFalse($result);
    }

    /**
     * @return array
     */
    private function setDependency()
    {
        return [$this->notifynderGroup];
    }
}
