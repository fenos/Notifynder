<?php

namespace spec\Fenos\Notifynder\Groups;

use Fenos\Notifynder\Contracts\NotifynderGroupCategoryDB;
use Fenos\Notifynder\Contracts\NotifynderGroupDB;
use Fenos\Notifynder\Exceptions\NotifynderGroupNotFoundException;
use Fenos\Notifynder\Models\NotificationGroup;
use PhpSpec\ObjectBehavior;

class GroupManagerSpec extends ObjectBehavior
{
    public function let(NotifynderGroupDB $groupDB, NotifynderGroupCategoryDB $groupCategoryDB)
    {
        $this->beConstructedWith($groupDB, $groupCategoryDB);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Groups\GroupManager');
    }

    /** @test */
    public function it_find_a_group_by_id(NotifynderGroupDB $groupDB, NotificationGroup $group)
    {
        $group_id = 1;

        $groupDB->find($group_id)->shouldBeCalled()
                ->willReturn($group);

        $this->findById($group_id)->shouldReturnAnInstanceOf(NotificationGroup::class);
    }

    /** @test */
    public function it_try_to_find_an_not_existing_group_by_id(NotifynderGroupDB $groupDB)
    {
        $group_id = 1;

        $groupDB->find($group_id)->shouldBeCalled()
            ->willReturn(null);

        $this->shouldThrow(NotifynderGroupNotFoundException::class)->during('findById', [$group_id]);
    }

    /** @test */
    public function it_find_a_group_by_name(NotifynderGroupDB $groupDB, NotificationGroup $group)
    {
        $group_name = 'mygroup';

        $groupDB->findByName($group_name)->shouldBeCalled()
            ->willReturn($group);

        $this->findByName($group_name)->shouldReturnAnInstanceOf(NotificationGroup::class);
    }

    /** @test */
    public function it_try_to_find_an_not_existing_group_by_name(NotifynderGroupDB $groupDB)
    {
        $group_name = 'mygroup';

        $groupDB->findByName($group_name)->shouldBeCalled()
            ->willReturn(null);

        $this->shouldThrow(NotifynderGroupNotFoundException::class)->during('findByName', [$group_name]);
    }

    /** @test */
    public function it_add_a_category_to_a_group_by_id(NotifynderGroupCategoryDB $groupCategoryDB, NotificationGroup $group)
    {
        $group_id = 1;
        $category_id = 2;

        $groupCategoryDB->addCategoryToGroupById($group_id, $category_id)->shouldBeCalled()
                ->willReturn($group);

        $this->addCategoryToGroupById($group_id, $category_id)->shouldReturnAnInstanceOf(NotificationGroup::class);
    }

    /** @test */
    public function it_add_a_category_to_a_group_by_name(NotifynderGroupCategoryDB $groupCategoryDB, NotificationGroup $group)
    {
        $group_name = 'mygroup';
        $category_name = 'mycategory';

        $groupCategoryDB->addCategoryToGroupByName($group_name, $category_name)->shouldBeCalled()
            ->willReturn($group);

        $this->addCategoryToGroupByName($group_name, $category_name)->shouldReturnAnInstanceOf(NotificationGroup::class);
    }

    /** @test */
    public function it_add_multiple_categories_to_a_group(NotifynderGroupCategoryDB $groupCategoryDB)
    {
        $group_name = 'mygroup';
        $category1 = 'mycategory1';
        $category2 = 'mycategory2';

        $groupCategoryDB->addMultipleCategoriesToGroup($group_name, [$category1, $category2])->shouldBeCalled()
                    ->willReturn(2);

        $this->addMultipleCategoriesToGroup($group_name, $category1, $category2)
                ->shouldReturn(2);
    }

    /** @test */
    public function it_add_a_group_in_the_db_respecting_convention(NotifynderGroupDB $groupDB, NotificationGroup $group)
    {
        $name = 'my.category';

        $groupDB->create($name)->shouldBeCalled()
                ->willReturn($group);

        $this->addGroup($name)->shouldReturnAnInstanceOf(NotificationGroup::class);
    }

    /** @test */
    public function it_add_a_group_in_the_db_NOT_respecting_convention(NotifynderGroupDB $groupDB, NotificationGroup $group)
    {
        $name = 'mycategory'; // no dot as 'namespace'

        $this->shouldThrow('InvalidArgumentException')->during('addGroup', [$name]);
    }
}
