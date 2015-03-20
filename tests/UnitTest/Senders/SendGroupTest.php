<?php

use Fenos\Notifynder\Senders\SendGroup;
use Mockery as m;

/**
 * Class SendGroupTest
 */
class SendGroupTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SendGroup
     */
    protected $sendGroup;

    /**
     * @var \Fenos\Notifynder\Notifynder
     */
    protected $notifynder;

    /**
     * @var \Fenos\Notifynder\Groups\NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var string
     */
    protected $groupName;

    /**
     * @var array
     */
    protected $dataNotification;

    /**
     * @var array
     */
    private $dependency = [];

    public function setUp()
    {
        $dataNotification = NotificationDataBuilder::multipleNotificationData(2);

        $this->sendGroup = new SendGroup(
            $this->notifynder = m::mock('Fenos\Notifynder\Notifynder'),
            $this->notifynderGroup = m::mock('Fenos\Notifynder\Groups\NotifynderGroup'),
            $this->groupName = 'group.name',
            $this->dataNotification = $dataNotification
        );

        $this->dependency = $this->setDependecy();
    }

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_send_a_group_of_notifications()
    {
        $mockSendGroup = m::mock('Fenos\Notifynder\Senders\SendGroup[sendLoop]', $this->dependency);

        $notification = m::mock('Fenos\Notifynder\Notifications\NotifynderNotification');

        $groupModel = m::mock('Fenos\Notifynder\Models\NotificationGroup');

        $this->notifynderGroup->shouldReceive('findGroupByName')
             ->once()
             ->with($this->groupName)
             ->andReturn($groupModel);

        $category = CategoryBuilderData::categoryData();

        $groupModel->shouldReceive('getAttribute')
             ->once()
             ->with('categories')
             ->andReturn([$category, $category]);

        $mockSendGroup->shouldReceive('sendLoop')
             ->times(2)
             ->with($category)
             ->andReturn($groupModel);

        $result = $mockSendGroup->send($notification);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup', $result);
    }

    /** @test */
    public function it_send_loops_the_notifications_giving_an_array()
    {
        $category_name = "category.to.send";

        $category = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $category->shouldReceive('getAttribute')
             ->once()
             ->with('name')
             ->andReturn($category_name);

        $this->notifynder->shouldReceive('category')
             ->once()
             ->with($category_name)
             ->andReturn($this->notifynder);

        $this->notifynder->shouldReceive('send')
             ->once()
             ->with($this->dataNotification)
             ->andReturn(2);

        $result = $this->sendGroup->sendLoop($category);

        $this->assertEquals(2, $result);
    }

    /** @test */
    public function it_send_loops_the_notifications_giving_an_closure()
    {
        $closure = function ($notifynder) {
            return true;
        };

        $mockSendGroup = m::mock('Fenos\Notifynder\Senders\SendGroup', $this->setDependecy($closure))->makePartial();

        $category_name = "category.to.send";

        $category = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $category->shouldReceive('getAttribute')
            ->once()
            ->with('name')
            ->andReturn($category_name);

        $result = $mockSendGroup->sendLoop($category);

        $this->assertTrue($result);
    }

    /**
     * Set depenency class
     *
     * @param  null  $dataNotification
     * @return array
     */
    private function setDependecy($dataNotification = null)
    {
        if (is_null($dataNotification)) {
            $dataNotification = $this->dataNotification;
        }

        return [$this->notifynder,$this->notifynderGroup,$this->groupName,$dataNotification];
    }
}
