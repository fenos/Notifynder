<?php

use Fenos\Notifynder\Groups\GroupRepository;
use Fenos\Notifynder\Models\NotificationGroup;

/**
 * Class GroupRepositoryTest.
 */
class GroupRepositoryTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var GroupRepository
     */
    protected $group;

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        parent::setUp();
        $this->group = app('notifynder.group.repository');
    }

    /** @test */
    public function it_find_a_group_by_id()
    {
        $group = $this->createGroup();

        $findGroup = $this->group->find($group->id);

        $this->assertEquals($group->id, $findGroup->id);
    }

    /** @test */
    public function it_find_a_group_by_name()
    {
        $group_name = 'mygroup';
        $this->createGroup(['name' => $group_name]);

        $group = $this->group->findByName($group_name);

        $this->assertEquals($group_name, $group->name);
    }

    /** @test */
    public function it_create_a_group()
    {
        $groupData = 'mygroup';

        $group = $this->group->create($groupData);

        $this->assertEquals($groupData, $group->name);
    }

    /** @test */
    public function it_delete_a_group_by_id()
    {
        $group = $this->createGroup();

        $this->group->delete($group->id);

        $this->assertCount(0, NotificationGroup::all());
    }
}
