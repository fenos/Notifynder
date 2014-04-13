<?php

use Mockery as m;

use Fenos\Notifynder\Notifynder;
use Fenos\Notifynder\Repositories\EloquentRepository\NotifynderCategoryRepository;
use Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException;

/**
* 
*/
class NofitynderCategoryRepositoryTest extends PHPUnit_Framework_TestCase
{

	/**
	* @var
	*/
	protected $notifynderRepository;

	/**
	* @var
	*/
	protected $notification_model;

	/**
	* @var
	*/
	protected $dbBuilder;

    public function setUp()
    {
        $model = m::mock('Illuminate\Database\Eloquent\Model');
        $app   = m::mock('Illuminate\Foundation\Application');

        $this->notification_model = m::mock('Fenos\Notifynder\Models\NotificationCategory');

        $this->notifynderRepository = new NotifynderCategoryRepository( 

        	$this->notification_model,
            $this->dbBuilder = m::mock('Illuminate\Database\DatabaseManager')
        	
        );

    }

    /**
    * Tear down function for all tests
    *
    */
    public function teardown()
    {
        m::close();
    }

    public function test_find_category_by_id()
    {
        $this->notification_model->shouldReceive('find')
                                ->once()
                                ->with(1)
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->find(1);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory',$result);
    }

    /**
    *@expectedException Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException
    */
    public function test_find_category_by_id_but_it_not_found()
    {
        $this->notification_model->shouldReceive('find')
                                ->once()
                                ->with(1)
                                ->andReturn(null);

        $this->notifynderRepository->find(1);
    }

    public function test_find_category_by_name_given()
    {
        $this->notification_model->shouldReceive('where')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('first')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->findByName('category');

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory',$result);
    }

    public function test_add_notification_category_to_db()
    {
        $category = [

            'name' => 'test category',
            'text' => 'this is the category test'
        ];

        $this->notification_model->shouldReceive('create')
                                ->once()
                                ->with($category)
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->add($category['name'],$category['text']);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory',$result);
    }

    public function test_delete_notification_category()
    {
        $this->notification_model->shouldReceive('find')
                                ->once()
                                ->with(1)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('delete')
                                ->once()
                                ->andReturn(true);

        $result = $this->notifynderRepository->delete(1);

        $this->assertTrue($result);
    }

    public function test_update_notification_category()
    {
        $category_new = [
            'name' => 'category new name',
            'text' => 'category new text'
        ];

        $this->notification_model->shouldReceive('find')
                                ->once()
                                ->with(1)
                                ->andReturn($this->notification_model);

        $this->notification_model->text = $category_new['text'];
        $this->notification_model->name = $category_new['name'];

        $this->notification_model->shouldReceive('save')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->update($category_new,1);

        $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory',$result);
    }

}