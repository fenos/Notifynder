<?php

use Fenos\Notifynder\Notifynder;
use Mockery as m;

/**
 * Class NotifynderTest
 */
class NotifynderTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Notifynder
     */
    protected $notifynder;

    /**
     * @var \Fenos\Notifynder\Categories\NotifynderCategory
     */
    protected $notifynderCategory;

    /**
     * @var \Fenos\Notifynder\Senders\NotifynderSender
     */
    protected $notifynderSender;

    /**
     * @var \Fenos\Notifynder\Notifications\NotifynderNotification
     */
    protected $notifynderNotification;

    /**
     * @var \Fenos\Notifynder\Handler\NotifynderHandler
     */
    protected $notifynderHandler;

    /**
     * @var \Fenos\Notifynder\Groups\NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var array
     */
    protected $dependency = [];

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->notifynder = new Notifynder(
            $this->notifynderCategory = m::mock('Fenos\Notifynder\Categories\NotifynderCategory'),
            $this->notifynderSender = m::mock('Fenos\Notifynder\Senders\NotifynderSender'),
            $this->notifynderNotification = m::mock('Fenos\Notifynder\Notifications\NotifynderNotification'),
            $this->notifynderHandler = m::mock('Fenos\Notifynder\Handler\NotifynderHandler'),
            $this->notifynderGroup = m::mock('Fenos\Notifynder\Groups\NotifynderGroup')
        );

        $this->setDependency();
    }

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_get_a_category_from_db()
    {
        $mockNotifynder = m::mock('Fenos\Notifynder\Notifynder[isEagerLoaded,getCategoriesContainer,setCategoriesContainer]',$this->dependency);

        $categoryName = 'testname';

        $categoryModel = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $mockNotifynder->shouldReceive('isEagerLoaded')
            ->once()
            ->with($categoryName)
            ->andReturn(false);

        $this->notifynderCategory->shouldReceive('findByName')
             ->once()
             ->with($categoryName)
             ->andReturn($categoryModel);

        $mockNotifynder->shouldReceive('setCategoriesContainer')
             ->once()
             ->with($categoryName,$categoryModel)
             ->andReturn($categoryModel);

        $result = $mockNotifynder->category($categoryName);

        $this->assertInstanceOf('Fenos\Notifynder\Notifynder',$result);
    }

    /** @test */
    public function it_get_a_category_egager_loaded()
    {
        $mockNotifynder = m::mock('Fenos\Notifynder\Notifynder[isEagerLoaded,getCategoriesContainer]',$this->dependency);

        $categoryName = 'testname';

        $categoryModel = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $mockNotifynder->shouldReceive('isEagerLoaded')
            ->once()
            ->with($categoryName)
            ->andReturn(true);

        $mockNotifynder->shouldReceive('getCategoriesContainer')
            ->once()
            ->andReturn([$categoryName => $categoryModel]);

        $result = $mockNotifynder->category($categoryName);

        $this->assertInstanceOf('Fenos\Notifynder\Notifynder',$result);
    }

    /** @test */
    public function it_send_both_notifications()
    {
        $singleNotificationData = NotificationDataBuilder::singleNotificationData();

        $this->notifynderSender->shouldReceive('send')
             ->once()
             ->with($singleNotificationData,null)
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->send($singleNotificationData);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_send_one_notification()
    {
        $singleNotificationData = NotificationDataBuilder::singleNotificationData();

        $this->notifynderSender->shouldReceive('sendOne')
            ->once()
            ->with($singleNotificationData,null)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->sendOne($singleNotificationData);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_send_multiple_notifications()
    {
        $singleNotificationData = NotificationDataBuilder::multipleNotificationData();

        $this->notifynderSender->shouldReceive('sendMultiple')
            ->once()
            ->with($singleNotificationData,null)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->sendMultiple($singleNotificationData);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_read_one_notification()
    {
        $notification_id = 1;

        $this->notifynderNotification->shouldReceive('readOne')
            ->once()
            ->with($notification_id)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->readOne($notification_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_read_limit_number_of_notifications()
    {
        $user_id = 1;

        $numbers = 10;

        $this->notifynderNotification->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notifynderNotification);

        $this->notifynderNotification->shouldReceive('readLimit')
            ->once()
            ->with($user_id,$numbers,"ASC")
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->readLimit($user_id,$numbers);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_read_all_notifications()
    {
        $user_id = 1;

        $this->notifynderNotification->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notifynderNotification);

        $this->notifynderNotification->shouldReceive('readAll')
            ->once()
            ->with($user_id)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->readAll($user_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_delete_a_single_notification()
    {
        $notification_id = 1;

        $this->notifynderNotification->shouldReceive('delete')
             ->once()
             ->with($notification_id)
             ->andReturn(true);

        $result = $this->notifynder->delete($notification_id);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_delete_notifications_limiting_a_the_given_number()
    {
        $user_id = 1;

        $numbers = 10;

        $this->notifynderNotification->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notifynderNotification);

        $this->notifynderNotification->shouldReceive('deleteLimit')
            ->once()
            ->with($user_id,$numbers,"ASC")
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->deleteLimit($user_id,$numbers);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_delete_all_notifications()
    {
        $user_id = 1;

        $this->notifynderNotification->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notifynderNotification);

        $this->notifynderNotification->shouldReceive('deleteAll')
            ->once()
            ->with($user_id)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->deleteAll($user_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_get_notification_not_read()
    {
        $user_id = 1;

        $limit = 10;

        $this->notifynderNotification->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notifynderNotification);

        $this->notifynderNotification->shouldReceive('getNotRead')
            ->once()
            ->with($user_id,$limit,false)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->getNotRead($user_id,$limit);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_get_all_notifications()
    {
        $user_id = 1;

        $limit = 10;

        $this->notifynderNotification->shouldReceive('entity')
            ->once()
            ->with(null)
            ->andReturn($this->notifynderNotification);

        $this->notifynderNotification->shouldReceive('getAll')
            ->once()
            ->with($user_id,$limit,false)
            ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->getAll($user_id,$limit);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_find_a_notification_by_ID()
    {
        $notification_id = 1;

        $this->notifynderNotification->shouldReceive('find')
             ->once()
             ->with($notification_id)
             ->andReturn(m::mock('Fenos\Notifynder\Models\Notification'));

        $result = $this->notifynder->findNotificationById($notification_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /** @test */
    public function it_add_a_category_in_a_group_giving_id_of_them()
    {
        $category_id = 1;
        $group_id = 1;

        $this->notifynderGroup->shouldReceive('addCategoryToGroupById')
            ->once()
            ->with($group_id,$category_id)
            ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $this->notifynder->addCategoryToGroupById($group_id,$category_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    /** @test */
    public function it_add_a_category_in_a_group_giving_name_of_them()
    {
        $category_name = 1;
        $group_name = 1;

        $this->notifynderGroup->shouldReceive('addCategoryToGroupByName')
            ->once()
            ->with($group_name,$category_name)
            ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $this->notifynder->addCategoryToGroupByName($group_name,$category_name);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    /** @test */
    public function it_add_multiple_categoris_in_a_group_giving_name_of_them()
    {
        $group_name = "notifynder";
        $category_name = ["notifynder1","notifynder2"];

        $this->notifynderGroup->shouldReceive('addMultipleCategoriesToGroup')
            ->once()
            ->with([$group_name,$category_name])
            ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $this->notifynder->addCategoriesToGroup($group_name,$category_name);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    /** @test */
    public function it_fire_a_event_listener()
    {
        $key = "user.post.add";
        $category_name = "category.notification";

        $this->notifynderHandler->shouldReceive('fire')
             ->once()
             ->with($this->notifynder,$key,$category_name,[])
             ->andReturn(true);

        $result = $this->notifynder->fire($key,$category_name,[]);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_delegate_a_event_listener()
    {
        $delegation = [
            'category.name' => 'event.key.name',
            'category2.name' => 'event.key2.name'
        ];

        $data = NotificationDataBuilder::multipleNotificationData(3);

        $this->notifynderHandler->shouldReceive('delegate')
            ->once()
            ->with($this->notifynder,$data,$delegation)
            ->andReturn(true);

        $result = $this->notifynder->delegate($data,$delegation);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_check_array_on_property_for_eager_loading()
    {
        $mockNotifynder = m::mock('Fenos\Notifynder\Notifynder[getCategoriesContainer]',$this->dependency);

        $categoryName = 'testname';

        $mockNotifynder->shouldReceive('getCategoriesContainer')
            ->once()
            ->andReturn([$categoryName => "yes"]);

        $result = $mockNotifynder->isEagerLoaded($categoryName);

        $this->assertTrue($result);
    }

    /**
     * Set Dependency of the class
     */
    private function setDependency()
    {
        $this->dependency = [
                $this->notifynderCategory,
                $this->notifynderSender,
                $this->notifynderNotification,
                $this->notifynderHandler,
                $this->notifynderGroup
        ];
    }
}
 