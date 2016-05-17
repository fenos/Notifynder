<?php

use Fenos\Notifynder\Categories\CategoryRepository;
use Laracasts\TestDummy\Factory;

/**
 * Class NotificationCategoryRepositoryTest.
 */
class NotificationCategoryRepositoryTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepo;

    /**
     * SetUp Tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->categoryRepo = app('notifynder.category.repository');
    }

    /** @test */
    public function it_find_a_category_by_id()
    {
        $record = $this->createCategory();

        $category = $this->categoryRepo->find($record->id);

        $this->assertEquals(1, $category->id);
    }

    /** @test */
    public function it_find_a_category_by_name()
    {
        $categoryName = 'test.category';

        $this->createCategory(['name' => $categoryName]);

        $category = $this->categoryRepo->findByName($categoryName);

        $this->assertEquals($categoryName, $category->name);
    }

    /** @test */
    public function it_find_categories_giving_multiple_names()
    {
        $categoryNames = ['test.first', 'test.second'];

        $this->createCategory(['name' => $categoryNames[0]]);
        $this->createCategory(['name' => $categoryNames[1]]);

        $categories = $this->categoryRepo->findByNames($categoryNames);

        $this->assertCount(2, $categories);
    }

    /** @test */
    public function it_add_a_new_category()
    {
        $categoryData = Factory::build('Fenos\Notifynder\Models\NotificationCategory');

        $createCategory = $this->categoryRepo->add($categoryData->name, $categoryData->text);

        $this->assertEquals($categoryData->name, $createCategory->name);
    }

    /** @test */
    public function it_delete_a_category_by_id()
    {
        $categoryToDelete = $this->createCategory();

        $this->categoryRepo->delete($categoryToDelete->id);

        $tryToFindThatCategory = $this->categoryRepo->find($categoryToDelete->id);

        $this->assertEquals($tryToFindThatCategory, null);
    }

    /** @test */
    public function it_delete_a_category_by_name()
    {
        $categoryToDelete = $this->createCategory();

        $this->categoryRepo->deleteByName($categoryToDelete->name);

        $tryToFindThatCategory = $this->categoryRepo->findByName($categoryToDelete->name);

        $this->assertEquals($tryToFindThatCategory, null);
    }
}
