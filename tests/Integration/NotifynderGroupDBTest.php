<?php

use Fenos\Notifynder\Models\NotificationGroup;
use Laracasts\TestDummy\Factory as TestDummy;

class NotifynderGroupDBTest extends IntegrationDBTest {

    /**
     * @var NotificationGroup
     */
    protected $groupModel;

    /**
     * @var \Fenos\Notifynder\Groups\Repositories\NotificationGroupsRepository
     */
    protected $groupRepository;

    /**
     * SetUp Integration test
     */
    public function setUp()
    {
        parent::setUp();

        $this->groupModel = new NotificationGroup();
        $this->groupRepository = $this->app->make('notifynder.group.repository');
    }

    /** @test */
    public function it_create_a_group()
    {
        $group = TestDummy::build('Fenos\Notifynder\Models\NotificationGroup');

        $this->groupRepository->create($group);

        $groups = $this->groupModel->all();

        $this->assertCount(1,$groups);
    }

    /** @test */
    public function it_find_a_group_by_id()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationGroup');

        $group = $this->groupRepository->find(1);

        $this->assertEquals(1,$group->count());
        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$group);
    }

    /** @test */
    public function it_find_a_group_by_name()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationGroup',['name' => 'notifynder.pro']);

        $group = $this->groupRepository->findByName('notifynder.pro');

        $this->assertEquals(1,$group->count());
        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationGroup',$group);
    }

    /** @test */
    public function it_delete_a_group_by_id()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationGroup');

        $group = $this->groupRepository->delete(1);

        $groups = $this->groupModel->all();

        $this->assertEquals(1,$group);

        $this->assertCount(0,$groups);
    }
}