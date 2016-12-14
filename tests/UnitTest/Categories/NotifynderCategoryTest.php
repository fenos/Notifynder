<?php
/**
 * Created by Fabrizio Fenoglio.
 */
use Fenos\Notifynder\Categories\NotifynderCategory;
use Mockery as m;

/**
 * Class NotifynderCategoryTest.
 */
class NotifynderCategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var NotifynderCategory
     */
    protected $notifynderCategory;

    /**
     * @var \Fenos\Notifynder\Categories\Repositories\CategoryRepository
     */
    protected $categoryRepo;

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        $mockModel = m::mock('Illuminate\Database\Eloquent\Model');

        $this->notifynderCategory = new NotifynderCategory(
            $this->categoryRepo = m::mock('Fenos\Notifynder\Categories\Repositories\CategoryRepository')
        );
    }

    /**
     * TearDown.
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_find_a_category_by_name()
    {
        $notificationModel = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $nameCategory = 'testname';

        $this->categoryRepo->shouldReceive('findByName')
             ->once()
             ->with($nameCategory)
             ->andReturn($notificationModel);

        $result = $this->notifynderCategory->findByName($nameCategory);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory', $result);
    }

    /** @test */
    public function it_find_a_category_by_names()
    {
        $notificationModel = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $namesCategory = ['testname', 'testname2', 'testname3'];

        $this->categoryRepo->shouldReceive('findByNames')
            ->once()
            ->with($namesCategory)
            ->andReturn($notificationModel);

        $result = $this->notifynderCategory->findByNames($namesCategory);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory', $result);
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\CategoryNotFoundException
     */
    public function it_find_a_category_by_name_but_it_doesnt_exists()
    {
        $nameCategory = 'testname';

        $this->categoryRepo->shouldReceive('findByName')
            ->once()
            ->with($nameCategory)
            ->andReturn(null);

        $this->notifynderCategory->findByName($nameCategory);
    }

    /** @test */
    public function it_find_a_category_by_id()
    {
        $notificationModel = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $idCategory = 1;

        $this->categoryRepo->shouldReceive('find')
            ->once()
            ->with($idCategory)
            ->andReturn($notificationModel);

        $result = $this->notifynderCategory->find($idCategory);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory', $result);
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\CategoryNotFoundException
     */
    public function it_find_a_category_by_ID_but_it_doesnt_exists()
    {
        $idCategory = 'testname';

        $this->categoryRepo->shouldReceive('find')
            ->once()
            ->with($idCategory)
            ->andReturn(null);

        $this->notifynderCategory->find($idCategory);
    }

    /** @test */
    public function it_add_a_category()
    {
        $categoryInfo = CategoryBuilderData::categoryData();

        $modelCategory = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $this->categoryRepo->shouldReceive('add')
             ->once()
             ->with($categoryInfo)
             ->andReturn($modelCategory);

        $result = $this->notifynderCategory->add($categoryInfo);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory', $result);
    }

    /** @test */
    public function it_delete_a_category_from_db()
    {
        $categoryid = 1;

        $this->categoryRepo->shouldReceive('delete')
             ->once()
             ->with($categoryid)
             ->andReturn(true);

        $result = $this->notifynderCategory->delete($categoryid);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_delete_a_category_by_name()
    {
        $categoryName = 'testcategory';

        $this->categoryRepo->shouldReceive('deleteByName')
            ->once()
            ->with($categoryName)
            ->andReturn(true);

        $result = $this->notifynderCategory->deleteByName($categoryName);

        $this->assertTrue($result);
    }
}
