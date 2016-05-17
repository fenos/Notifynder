<?php

namespace spec\Fenos\Notifynder\Categories;

use Fenos\Notifynder\Contracts\CategoryDB;
use Fenos\Notifynder\Exceptions\CategoryNotFoundException;
use Fenos\Notifynder\Models\NotificationCategory;
use PhpSpec\ObjectBehavior;

class CategoryManagerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Categories\CategoryManager');
    }

    public function let(CategoryDB $categoryRepository)
    {
        $this->beConstructedWith($categoryRepository);
    }

    /** @test */
    public function it_find_a_category_by_name(CategoryDB $categoryRepository)
    {
        $nameCategory = 'test.category';

        $categoryRepository->findByName($nameCategory)->shouldBeCalled()
                           ->willReturn(new NotificationCategory());

        $this->findByName($nameCategory)->shouldReturnAnInstanceOf(NotificationCategory::class);
    }

    /** @test */
    public function it_try_to_find_a_non_existing_category(CategoryDB $categoryRepository)
    {
        $nameCategory = 'test.category';

        $categoryRepository->findByName($nameCategory)->shouldBeCalled()
            ->willReturn(null);

        $this->shouldThrow(CategoryNotFoundException::class)
             ->during('findByName', [$nameCategory]);
    }

    /** @test */
    public function it_store_a_category(CategoryDB $categoryRepository)
    {
        $categoryName = 'hello';
        $categoryText = 'wow';

        $categoryRepository->add($categoryName, $categoryText)->shouldBeCalled()
                           ->willReturn(new NotificationCategory());

        $this->add($categoryName, $categoryText)->shouldReturnAnInstanceOf(NotificationCategory::class);
    }

    /** @test */
    public function it_delete_a_category_by_id(CategoryDB $categoryRepository)
    {
        $categoryId = 1;

        $categoryRepository->delete($categoryId);

        $categoryRepository->delete($categoryId)->shouldBeCalled()
                           ->willReturn(1);

        $this->delete($categoryId)->shouldReturn(1);
    }

    /** @test */
    public function it_delete_a_category_by_name(CategoryDB $categoryRepository)
    {
        $categoryName = 'testCategory';

        $categoryRepository->deleteByName($categoryName)->shouldBeCalled()
                           ->willReturn(1);

        $this->deleteByName($categoryName)->shouldReturn(1);
    }
}
