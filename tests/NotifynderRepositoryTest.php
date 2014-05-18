<?php

use Mockery as m;

use Fenos\Notifynder\Notifynder;
use Fenos\Notifynder\Repositories\EloquentRepository\NotifynderRepository;
use Fenos\Notifynder\Exceptions\NotificationNotFoundException;

/**
*
*/
class NofitynderRepositoryTest extends PHPUnit_Framework_TestCase
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

        $this->notification_model = m::mock('Fenos\Notifynder\Models\Notification');

        $this->notifynderRepository = new NotifynderRepository(

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

    public function test_find_notification_by_id()
    {
        $this->notification_model->shouldReceive('find')
                                ->once()
                                ->with(1)
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->find(1);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    /**
    *@expectedException Fenos\Notifynder\Exceptions\NotificationNotFOundException
    */
    public function test_find_notification_but_it_doesnt_exist()
    {
        $this->notification_model->shouldReceive('find')
                                ->once()
                                ->with(1)
                                ->andReturn(null);

        $result = $this->notifynderRepository->find(1);
    }

    public function test_send_one_notification()
    {
        $oneNotification = array(
              'from_id'     => 1,
              'to_id'       => 2,
              'category_id' => 1,
              'url'         => 'www.test.com',
              'created_at'  => '2014-04-04 00:20:57',
              'updated_at'  => '2014-04-04 00:20:57'
            );

        $this->notification_model->shouldReceive('create')
                                ->once()
                                ->with($oneNotification)
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->sendOne($oneNotification);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_sending_multiple_notifications()
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

        $queryBuilder = m::mock('Illuminate\Database\Query\Builder');

        $this->dbBuilder->shouldReceive('table')
                    ->with('notifications')
                    ->andReturn($queryBuilder);

           $queryBuilder->shouldReceive('insert')
                       ->once()
                       ->andReturn(true);

        $result = $this->notifynderRepository->sendMultiple($notifications);

        $this->assertTrue($result);

    }

    public function test_one_notification()
    {

      $this->notification_model->shouldReceive('find')
                            ->once()
                            ->with(1)
                            ->andReturn($this->notification_model);

      $this->notification_model->shouldReceive('save')
                            ->once()
                            ->andReturn($this->notification_model);

      $result = $this->notifynderRepository->readOne(1);

      $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);


    }

    public function test_read_notification_giving_a_max_limit()
    {
        $queryBuilder = m::mock('Illuminate\Database\Query\Builder');

        $this->dbBuilder->shouldReceive('table')
                        ->once()
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('where')
                        ->with('to_id','=',2)
                        ->once()
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('orderBy')
                        ->with('id','ASC')
                        ->once()
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('limit')
                        ->with(10)
                        ->once()
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('update')
                ->once()
                ->andReturn(10);

        $result = $this->notifynderRepository->readLimit(2,10,'ASC');
        $this->assertEquals(10,$result);
    }

    public function test_read_all_notifications()
    {

        $queryBuilder = m::mock('Illuminate\Database\Query\Builder');

        $this->dbBuilder->shouldReceive('table')
                        ->once()
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('where')
                        ->with('to_id','=',1)
                        ->once()
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('update')
                ->once()
                ->andReturn(1);

        $result = $this->notifynderRepository->readAll(1);
        $this->assertEquals(1,$result);

    }

    public function test_delete_a_single_notification()
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

    public function test_delete_all_notifications_of_this_user()
    {
        $queryBuilder = m::mock('Illuminate\Database\Query\Builder');

        $this->dbBuilder->shouldReceive('table')
                        ->once()
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('where')
                        ->once('to_id',1)
                        ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('delete')
                        ->once()
                        ->andReturn(true);

        $result = $this->notifynderRepository->deleteAll(1);

        $this->assertTrue($result);
    }

    public function test_delete_limiting_the_results_as_specificated_and_by_order()
    {
        $this->notification_model->shouldReceive('where')
                                ->once()
                                ->with('to_id',1)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('orderBy')
                                ->once()
                                ->with('id','DESC')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('select')
                                ->once()
                                ->with('id')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('limit')
                                ->once()
                                ->with(10)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('get')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('count')
                                ->once()
                                ->andReturn(10);

        $array_ids = [0 => 1,1 => 2, 2=> 3,3 => 4,4 => 5,5 => 6,6 =>7,7 => 8,8 => 9,9 => 10];

        $array = $this->notification_model->shouldReceive('toArray')
                                ->once()
                                ->andReturn($array_ids);



        $flatten = m::mock(['array_flatten']);
        $flatten->shouldReceive('array_flatten')
                    ->with($array_ids)
                    //improve this test it is bit tricky
                    ->andReturn([0,1,2,3,4,5,6,7,8,9,10]);

        $this->notification_model->shouldReceive('whereIn')
                                ->once()
                                //improve this test little it is bit tricky
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('delete')
                                ->once()
                                ->andReturn(true);

        $result = $this->notifynderRepository->deleteLimit(1,10,'DESC');

        $this->assertTrue($result);
    }

    public function test_get_all_not_read_notifications()
    {
        $this->notification_model->shouldReceive('with')
                                ->with('body','from')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('where')
                                ->times(2)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('orderBy')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('get')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('parse')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->getNotRead(1,null,false);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_get_limit_not_read_notifications()
    {
        $this->notification_model->shouldReceive('with')
                                ->with('body','from')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('where')
                                ->times(2)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('orderBy')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('limit')
                                ->once()
                                ->with(10)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('get')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('parse')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->getNotRead(1,10,false);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_get_pagination_not_read_notifications()
    {
        $this->notification_model->shouldReceive('with')
                                ->with('body','from')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('where')
                                ->times(2)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('orderBy')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('paginate')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('parse')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->getNotRead(1,10,true);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_get_all_notifications()
    {
        $this->notification_model->shouldReceive('with')
                                ->with('body','from')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('where')
                                ->once()
                                ->with('to_id',1)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('orderBy')
                                ->times(2)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('get')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('parse')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->getAll(1,null,false);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_get_all_notifications_limiting_result()
    {
        $this->notification_model->shouldReceive('with')
                                ->with('body','from')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('where')
                                ->with('to_id',1)
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('orderBy')
                                ->times(2)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('limit')
                                ->with(10)
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('get')
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('parse')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->getAll(1,10,false);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

    public function test_get_all_notifications_paginating_result()
    {
        $this->notification_model->shouldReceive('with')
                                ->with('body','from')
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('where')
                                ->with('to_id',1)
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('orderBy')
                                ->times(2)
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('paginate')
                                ->with(10)
                                ->once()
                                ->andReturn($this->notification_model);

        $this->notification_model->shouldReceive('parse')
                                ->once()
                                ->andReturn($this->notification_model);

        $result = $this->notifynderRepository->getAll(1,10,true);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$result);
    }

}
