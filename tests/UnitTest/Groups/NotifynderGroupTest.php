<?php

use Fenos\Notifynder\Groups\NotifynderGroup;
use Mockery as m;

/**
 * Class NotifynderGroupTest
 */
class NotifynderGroupTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var NotifynderGroup
     */
    protected $notifynderGroup;

    /**
     * @var \Fenos\Notifynder\Groups\Repositories\NotificationGroupsRepository
     */
    protected $groupRepo;

    /**
     * @var \Fenos\Notifynder\Groups\Repositories\NotificationGroupCategoryRepository
     */
    protected $groupCategory;

    /**
     * @var array
     */
    protected $dependency = [];

    /**
     * SetUp UnitTest
     */
    public function setUp()
    {
        $this->notifynderGroup = new NotifynderGroup(
            $this->groupRepo = m::mock('Fenos\Notifynder\Groups\Repositories\NotificationGroupsRepository'),
            $this->groupCategory = m::mock('Fenos\Notifynder\Groups\Repositories\NotificationGroupCategoryRepository')
        );

        $this->dependency = $this->setDependency();
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException
     * */
    public function it_find_a_group_by_id_but_it_doesnt_exists()
    {
        $group_id = 1;

        $this->groupRepo->shouldReceive('find')
             ->once()
             ->with($group_id)
             ->andReturn(null);

        $this->notifynderGroup->findGroupById($group_id);
    }

    /** @test */
    public function it_find_a_group_by_id()
    {
        $group_id = 1;

        $this->groupRepo->shouldReceive('find')
            ->once()
            ->with($group_id)
            ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $this->notifynderGroup->findGroupById($group_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    /** @test */
    public function it_find_a_group_by_name()
    {
        $group_name = "event.group";

        $this->groupRepo->shouldReceive('findByName')
            ->once()
            ->with($group_name)
            ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $this->notifynderGroup->findGroupByName($group_name);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    /** @test */
    public function it_add_category_to_a_group_giving_ids_of_them()
    {
        $group_id = 1;
        $category_id = 1;

        $this->groupCategory->shouldReceive('addCategoryToGroupById')
             ->once()
             ->with($group_id,$category_id)
             ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $this->notifynderGroup->addCategoryToGroupById($group_id,$category_id);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    /** @test */
    public function it_add_category_to_a_group_giving_names_of_them()
    {
        $group_name = "notifynder.event";
        $category_name = "notifynder";

        $this->groupCategory->shouldReceive('addCategoryToGroupByName')
            ->once()
            ->with($group_name,$category_name)
            ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $this->notifynderGroup->addCategoryToGroupByName($group_name,$category_name);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    /** @test */
    public function it_add_a_group_in_the_db()
    {
        $name = "notifynder.hello";

        $mockGroup = m::mock('Fenos\Notifynder\Groups\NotifynderGroup[isStringWithDots]',$this->dependency);

        $mockGroup->shouldReceive('isStringWithDots')
             ->once()
             ->with($name)
             ->andReturn(true);

        $this->groupRepo->shouldReceive('create')
             ->once()
             ->with($name)
             ->andReturn(m::mock('Fenos\Notifynder\Models\NotificationGroup'));

        $result = $mockGroup->addGroup($name);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$result);
    }

    public function setDependency()
    {
        return [$this->groupRepo,$this->groupCategory];
    }
}
 