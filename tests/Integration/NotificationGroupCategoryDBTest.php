<?php

use Fenos\Notifynder\Models\NotificationGroup;
use Laracasts\TestDummy\Factory as TestDummy;

class NotificationGroupCategoryDBTest extends IntegrationDBTest {

    /**
     * @var NotificationGroup
     */
    protected $groupModel;

    /**
     * @var \Fenos\Notifynder\Categories\NotifynderCategory
     */
    protected $categoryRepo;

    /**
     * @var \Fenos\Notifynder\Groups\Repositories\NotificationGroupCategoryRepository
     */
    protected $groupCategory;

    /**
     * SetUp Integration test
     */
    public function setUp()
    {
        parent::setUp();

        $this->groupModel = new NotificationGroup();
        $this->categoryRepo = $this->app->make('notifynder.category');
        $this->groupCategory = $this->app->make('notifynder.group.category-repository');
    }

    /** @test */
    public function it_add_a_category_in_a_group_giving_the_ids()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationGroup',['id' => 1]);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['id' => 1,'name' => 'notifynder']);

        $this->groupCategory->addCategoryToGroupById(1,1);

        $groupCategories = $this->groupModel->with('categories')->find(1);

        $this->assertEquals(1,$groupCategories->count());

        $this->assertEquals('notifynder',$groupCategories->categories[0]->name);
    }

    /** @test */
    public function it_add_a_category_in_a_group_giving_the_names()
    {
        $category_name = "notifynder";
        $group_name = "notifynder.event";

        TestDummy::create('Fenos\Notifynder\Models\NotificationGroup',['id' => 1, 'name' => $group_name]);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['id' => 1,'name' => $category_name]);

        $this->groupCategory->addCategoryToGroupByName($group_name,$category_name);

        $groupCategories = $this->groupModel->with('categories')->find(1);

        $this->assertEquals(1,$groupCategories->count());

        $this->assertEquals($category_name,$groupCategories->categories[0]->name);
    }

    /** @test */
    public function it_add_multiple_categories_in_a_group_giving_them_names()
    {
        $categories_name = ["notifynder","notifynder2","notifynder3"];
        $group_name = "notifynder.event";

        TestDummy::create('Fenos\Notifynder\Models\NotificationGroup',['id' => 1, 'name' => $group_name]);

        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['id' => 1,'name' => $categories_name[0]]);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['id' => 2,'name' => $categories_name[1]]);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['id' => 3,'name' => $categories_name[2]]);

        $this->groupCategory->addMultipleCategoriesToGroup($group_name,$categories_name);

        $groupCategories = $this->groupModel->with('categories')->find(1);

        $this->assertEquals(3,$groupCategories->categories->count());

        $this->assertEquals($categories_name[0],$groupCategories->categories[0]->name);
    }
} 