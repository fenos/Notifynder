<?php

use Mockery as m;
use Carbon\Carbon;


use Fenos\Notifynder\Models\Notification;

use Fenos\Notifynder\Notifynder;
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;
use Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException;


/**
*
*/
class NofifynderTest extends PHPUnit_Framework_TestCase
{

    /**
    * @var Fenos\Notifynder\Repositories\NotifynderRepositoryInterface
    */
    protected $notifynderRepository;

    /**
    * @var Fenos\Notifynder\Repositories\NotifynderTypeRepositoryInterface
    */
    protected $notifynderTypeRepository;

    /**
    * @var Fenos\Notifynder\Notifynder
    */
    protected $notifynder;

    /**
    * @var Fenos\Notifynder\Models\NotificationCategory
    */
    protected $notification_category_model;

    /**
    * @var instance of Fenos\Notifynder\Translator\NotifynderTranslator
    */
    protected $notifynderTranslator;

    /**
    * @var instance of Fenos\Notifynder\Handler\NotifynderHandler
    */
    protected $notifynderHandler;

    /**
    * @var Fenos\Notifynder\Models\Notificantion
    */
    protected $notification_model;

    public function setUp()
    {

        $this->notification_model = m::mock('Illuminate\Database\Eloquent\Model');
        $this->notification_category_model = m::mock('Fenos\Notifynder\Models\NotificationCategory')->makePartial();

        $this->notifynder = new Notifynder(

            $this->notifynderRepository = m::mock('Fenos\Notifynder\Repositories\EloquentRepository\NotifynderRepository'),
            $this->notifynderTypeRepository = m::mock('Fenos\Notifynder\Repositories\EloquentRepository\NotifynderCategoryRepository'),
            $this->notifynderTranslator = m::mock('Fenos\Notifynder\Translator\NotifynderTranslator'),
            $this->notifynderHandler = m::mock('Fenos\Notifynder\Handler\NotifynderHandler')

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

    public function test_trasform_category_from_name_to_id()
    {

      $this->notifynderTypeRepository->shouldReceive('findByName')
                          ->once()
                          ->with('comment')
                          ->andReturn($this->notification_category_model);


      $this->notification_category_model->id = 1;

      $result = $this->notifynder->category('comment');

      $this->assertInstanceOf('Fenos\Notifynder\Notifynder',$result);
    }

    /**
    *@expectedException Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException
    */
    public function test_trasform_category_from_name_to_id_on_not_found_category()
    {
      $this->notifynderTypeRepository->shouldReceive('findByName')
                          ->once()
                          ->with('comment')
                          ->andReturn(null);

      $result = $this->notifynder->category('comment');

    }

    public function test_send_single_notification_with_id()
    {
        $oneNotification = array(
              'from_id'     => 1,
              'to_id'       => 2,
              'category_id' => 1,
              'url'         => 'www.test.com',
              'created_at'  => '2014-04-04 00:20:57',
              'updated_at'  => '2014-04-04 00:20:57'
        );

        $this->notifynderRepository->shouldReceive('sendOne')
                                    ->once()
                                    ->with($oneNotification)
                                    ->andReturn( m::mock('Fenos\Notifynder\Models\Notification') );

        $result = $this->notifynder->sendOne($oneNotification);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /**
    * @expectedException Fenos\Notifynder\Exceptions\NotificationNotFoundException
    */
    public function test_send_one_without_specific_id()
    {
        $oneNotification = array(
              'from_id'     => 1,
              'to_id'       => 2,
              'category_id' => 1,
              'url'         => 'www.test.com',
              'created_at'  => '2014-04-04 00:20:57',
              'updated_at'  => '2014-04-04 00:20:57'
        );

        $this->notifynderRepository->shouldReceive('sendOne')
                                    ->once()
                                    ->with($oneNotification)
                                    ->andThrow(new NotificationNotFoundException);

        $result = $this->notifynder->sendOne($oneNotification);
    }

    public function test_send_multiple_notifications()
    {
        $notifications = [

            array(
              'from_id'     => 1,
              'to_id'       => 2,
              'category_id' => 1,
              'url'         => 'www.test.com',
              'created_at'  => '2014-04-04 00:20:57',
              'updated_at'  => '2014-04-04 00:20:57'
            ),

            array(
              'from_id'     => 3,
              'to_id'       => 2,
              'category_id'     => 1,
              'url'         => 'www.test.com',
              'created_at'  => '2014-04-04 00:20:57',
              'updated_at'  => '2014-04-04 00:20:57'
            ),

            array(
              'from_id'     => 1,
              'to_id'       => 4,
              'category_id'     => 1,
              'url'         => 'www.test.com',
              'created_at'  => '2014-04-04 00:20:57',
              'updated_at'  => '2014-04-04 00:20:57'
            ),
        ];

        $this->notifynderRepository->shouldReceive('sendMultiple')
                                    ->once()
                                    ->with($notifications)
                                    ->andReturn(3);

        $result = $this->notifynder->sendMultiple($notifications);

        $this->assertEquals(3,$result);
    }

    public function test_read_one_notifiaction_by_id()
    {
      $this->notifynderRepository->shouldReceive('readOne')
                                  ->once()
                                  ->with(1)
                                  ->andReturn( m::mock('Fenos\Notifynder\Models\Notification') );

      $result = $this->notifynder->readOne(1);

      $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /**
    *@expectedException Fenos\Notifynder\Exceptions\NotificationNotFoundException
    */
    public function test_read_one_notification_with_id_not_existing()
    {
      $this->notifynderRepository->shouldReceive('readOne')
                                  ->once()
                                  ->with(1)
                                  ->andReturn(false);

      $result = $this->notifynder->readOne(1);
    }

    public function test_read_notifications_giving_max_limit()
    {

      $this->notifynderRepository->shouldReceive('entity')
                ->once()
                ->with("")
                ->andReturn($this->notifynderRepository);

      $this->notifynderRepository->shouldReceive('readLimit')
                                  ->once()
                                  ->with(2,10,'ASC')
                                  ->andReturn(10);

      $result = $this->notifynder->readLimit(2,10,'ASC');

      $this->assertEquals(10,$result);
    }

    public function test_read_all_notifications_by_user_id()
    {
      $this->notifynderRepository->shouldReceive('entity')
                ->once()
                ->with("")
                ->andReturn($this->notifynderRepository);

      $this->notifynderRepository->shouldReceive('readAll')
                                  ->once()
                                  ->with(1)
                                  ->andReturn(5);

      $result = $this->notifynder->readAll(1);

      $this->assertEquals(5,$result);
    }

    public function test_get_not_read_notifications()
    {
      $this->notifynderRepository->shouldReceive('entity')
                ->once()
                ->with("")
                ->andReturn($this->notifynderRepository);

      $this->notifynderRepository->shouldReceive('getNotRead')
                                  ->once()
                                  ->with(1,null,false)
                                  ->andReturn( m::mock('Fenos\Notifynder\Models\Notification') );

      $result = $this->notifynder->getNotRead(1,null,false);

      $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_get_all_notifications()
    {
      $this->notifynderRepository->shouldReceive('entity')
                ->once()
                ->with("")
                ->andReturn($this->notifynderRepository);

      $this->notifynderRepository->shouldReceive('getAll')
                                  ->once()
                                  ->with(1,null,false)
                                  ->andReturn( m::mock('Fenos\Notifynder\Models\Notification') );

      $result = $this->notifynder->getAll(1,null,false);

      $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_delete_a_single_notification()
    {
      $this->notifynderRepository->shouldReceive('delete')
                                  ->once()
                                  ->with(1)
                                  ->andReturn(true);

      $result = $this->notifynder->delete(1);

      $this->assertTrue($result);
    }

    public function test_delete_all_notifications_about_the_current_user()
    {
      $this->notifynderRepository->shouldReceive('entity')
                ->once()
                ->with("")
                ->andReturn($this->notifynderRepository);

      $this->notifynderRepository->shouldReceive('deleteAll')
                                  ->once()
                                  ->with(1)
                                  ->andReturn(true);

      $result = $this->notifynder->deleteAll(1);

      $this->assertTrue($result);
    }

    public function test_delete_notifications_as_much_as_the_number_and_order_passes()
    {
      $this->notifynderRepository->shouldReceive('entity')
                ->once()
                ->with("")
                ->andReturn($this->notifynderRepository);

      $this->notifynderRepository->shouldReceive('deleteLimit')
                                  ->once()
                                  ->with(1,10,'DESC')
                                  ->andReturn(true);

      $result = $this->notifynder->deleteLimit(1,10,'DESC');

      $this->assertTrue($result);
    }

    public function test_add_category_notification()
    {
      $this->notifynderTypeRepository->shouldReceive('add')
                                  ->once()
                                  ->with('comment','The user has added a comment on your post')
                                  ->andReturn($this->notification_category_model);

      $result = $this->notifynder->addCategory('comment','The user has added a comment on your post');

      $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory',$result);
    }

    public function test_delete_category_notification()
    {
      $this->notifynderTypeRepository->shouldReceive('delete')
                                  ->once()
                                  ->with(1)
                                  ->andReturn( m::mock('Fenos\Notifynder\Models\Notification') );

      $result = $this->notifynder->deleteCategory(1);

      $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /**
    *@expectedException Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException
    */
    public function test_delete_category_without_specific_id()
    {
      $result = $this->notifynder->deleteCategory();
    }

    public function test_translate_notification()
    {
      $this->notifynderTranslator->shouldReceive('translate')
                                ->once()
                                ->with('it','notifynder')
                                ->andReturn('Notifynder e\' magnifico');

      $result = $this->notifynder->translate('it','notifynder');

      $this->assertEquals($result,'Notifynder e\' magnifico');

    }

    public function test_update_type_notification()
    {
      $this->notifynderTypeRepository->shouldReceive('update')
                                  ->once()
                                  ->with(['name' => 'new', 'content' => 'new content'],1)
                                  ->andReturn($this->notification_category_model);

      $result = $this->notifynder->updateCategory(['name' => 'new', 'content' => 'new content'],1);

      $this->assertInstanceOf('Fenos\Notifynder\Models\NotificationCategory',$result);
    }

    public function test_listen_handler()
    {
      $this->notifynderHandler->shouldReceive('listen')
                            ->once()
                            ->with(['key' => 'test', 'handler' => 'Test@test'])
                            ->andReturn(['test' => 'Test@test']);

      $result = $this->notifynder->listen(['key' => 'test', 'handler' => 'Test@test']);

      $this->assertEquals(['test' => 'Test@test'],$result);
    }
}
