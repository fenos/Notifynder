<?php

use Fenos\Notifynder\Groups\GroupCategoryRepository;

/**
 * Class GroupCategoryReposutoryTest.
 */
class GroupCategoryReposutoryTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var GroupCategoryRepository
     */
    protected $categoryGroup;

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->categoryGroup = app('notifynder.group.category');
    }

    /** @test */
    public function it_add_a_category_to_a_group_id()
    {
        $category = $this->createCategory();
        $group = $this->createGroup();

        $this->categoryGroup->addCategoryToGroupById(
            $group->id,
            $category->id
        );

        $this->assertEquals($group->categories[0]->name, $category->name);
    }

    /** @test */
    public function it_add_a_category_to_a_group_by_name()
    {
        $category = $this->createCategory();
        $group = $this->createGroup();

        $this->categoryGroup->addCategoryToGroupByName(
            $group->name,
            $category->name
        );

        $this->assertEquals($group->categories[0]->name, $category->name);
    }

    /** @test */
    public function it_add_multiple_categories_to_a_group_by_name()
    {
        $category1 = $this->createCategory();
        $category2 = $this->createCategory();
        $group = $this->createGroup();

        $this->categoryGroup->addMultipleCategoriesToGroup(
            $group->name,
            [$category1->name, $category2->name]
        );

        $this->assertCount(2, $group->categories);
    }
}
