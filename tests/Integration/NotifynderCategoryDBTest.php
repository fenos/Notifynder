<?php

use Fenos\Notifynder\Models\NotificationCategory;
use Laracasts\TestDummy\Factory as TestDummy;

/**
 * Created by Fabrizio Fenoglio.
 */
class NotifynderCategoryDBTest extends IntegrationDBTest
{
    /**
     * @var NotificationCategory
     */
    protected $category;

    /**
     * @var \Fenos\Notifynder\Categories\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SetUp Integration Test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->category = new NotificationCategory();
        $this->categoryRepository = $this->app->make('notifynder.category.repository');
    }

    /** @test */
    public function it_find_a_notification_by_id()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory');

        $category = $this->categoryRepository->find(1);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory', $category);

        $this->assertEquals(1, $category->count());
    }

    /** @test */
    public function it_find_a_notification_by_name()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory', ['name' => 'notifynder']);

        $category = $this->categoryRepository->findByName('notifynder');

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory', $category);

        $this->assertEquals(1, $category->count());
    }

    /** @test */
    public function it_find_a_notification_by_names()
    {
        $names = ['notifynder1', 'notifynder2', 'notifynder3', 'notifynder4'];

        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory', ['name' => 'notifynder1']);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory', ['name' => 'notifynder2']);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory', ['name' => 'notifynder3']);
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory', ['name' => 'notifynder4']);

        $category = $this->categoryRepository->findByNames($names);

        $this->assertCount(4, $category);
    }

    /** @test */
    public function it_add_a_category()
    {
        $category = TestDummy::build('Fenos\Notifynder\Models\NotificationCategory');

        $this->categoryRepository->add($category->toArray());

        $allCategories = $this->category->all();

        $this->assertCount(1, $allCategories);
    }

    /** @test */
    public function it_delete_a_category()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory');

        $categoryDelete = $this->categoryRepository->delete(1);

        $category = $this->category->all();

        $this->assertEquals(1, $categoryDelete);

        $this->assertCount(0, $category);
    }

    /** @test */
    public function it_delete_a_category_by_name()
    {
        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory', ['name' => 'notifynder']);

        $categoryDelete = $this->categoryRepository->deleteByName('notifynder');

        $category = $this->category->all();

        $this->assertEquals(1, $categoryDelete);

        $this->assertCount(0, $category);
    }
}
