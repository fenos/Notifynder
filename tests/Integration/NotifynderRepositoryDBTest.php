<?php

use Fenos\Notifynder\Models\Notification;
use Laracasts\TestDummy\Factory as TestDummy;

class NotifynderRepositoryDBTest extends IntegrationDBTest {

    /**
     * @var Notification
     */
    protected $notificationModel;

    /**
     * @var \Fenos\Notifynder\Notifications\Repositories\NotificationRepository
     */
    protected $notifynderRepository;

    /**
     * Set Up Integration Test
     */
    public function setUp()
    {
        parent::setUp();

        $this->notificationModel = new Notification();
        $this->notifynderRepository = $this->app->make('notifynder.notification.repository');
    }

    /** @test */
    public function it_find_a_notification_by_id()
    {
        TestDummy::create('Fenos\Notifynder\Models\Notification');

        $notification = $this->notifynderRepository->find(1);

        $this->assertInstanceOf('Fenos\Notifynder\Models\Notification',$notification);

        $this->assertSame(1,$notification->count());
    }

    /** @test */
    public function it_send_a_single_notification_using_send()
    {
        $info = TestDummy::build('Fenos\Notifynder\Models\Notification',['category_id' => 1]);

        $this->notifynderRepository->sendSingle($info->toArray());

        $allNotifications = $this->notificationModel->all();

        $this->assertSame(1,$allNotifications->count());
    }

    /** @test */
    public function it_send_multiple_notifications_using_send()
    {
        for($i = 0; $i < 6; $i++)
        {
            $info[$i] = TestDummy::build('Fenos\Notifynder\Models\Notification',['category_id' => 1])->toArray();
        }

        $this->notifynderRepository->sendMultiple($info);

        $allNotifications = $this->notificationModel->get();

        $this->assertCount(6,$allNotifications);
    }

    /** @test */
    public function it_send_one_notification_setting_category_by_method()
    {
        $infoNotification = TestDummy::build('Fenos\Notifynder\Models\Notification',['category_id' => 1])->toArray();

        TestDummy::create('Fenos\Notifynder\Models\NotificationCategory',['name' => 'name']);

        Notifynder::category('name')->sendOne($infoNotification);

        $all = Notification::all();

        $this->assertCount(1,$all);
    }

    /** @test */
    public function it_send_one_notification()
    {
        $infoNotification = TestDummy::build('Fenos\Notifynder\Models\Notification',['category_id' => 1]);

        $this->notifynderRepository->sendSingle($infoNotification->toArray());

        $all = $this->notificationModel->all();

        $this->assertCount(1,$all);
    }

    /** @test */
    public function it_send_multiple_notifications()
    {
        for($i = 0; $i < 6; $i++)
        {
            $info[$i] = TestDummy::build('Fenos\Notifynder\Models\Notification',['category_id' => 1])
                                   ->toArray();
        }

        $this->notifynderRepository->sendMultiple($info);

        $allNotifications = $this->notificationModel->get();

        $this->assertCount(6,$allNotifications);
    }

    /** @test */
    public function it_read_one_notification()
    {
        TestDummy::create('Fenos\Notifynder\Models\Notification');

        $notification = $this->notificationModel->find(1);

        $read = $this->notifynderRepository->readOne($notification);

        $this->assertEquals(1,$read->read);
    }

    /** @test */
    public function it_read_all_the_notifications_the_number()
    {
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $this->notifynderRepository->readAll(1);

        $notRead = Notifynder::getNotRead(1);

        $this->assertCount(0,$notRead);
    }

//    /** @test */
//    public function it_read_limiting_the_notifications_of_the_given_number()
//    {
//        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);
//
//        Notifynder::readLimit(1,5);
//
//        $notRead = Notifynder::getNotRead(1);
//
//        $this->assertCount(5,$notRead);
//    }

    /** @test */
    public function it_delete_one_notification_by_id_given()
    {
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $this->notifynderRepository->delete(1);

        $allNotifications = $this->notificationModel->all();

        $this->assertCount(9,$allNotifications);
    }

    /** @test */
    public function it_delete_all_notifications_of_the_given_entity()
    {
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $this->notifynderRepository->deleteAll(1);

        $allNotifications = $this->notificationModel->all();

        $this->assertCount(0,$allNotifications);
    }

    /** @test */
    public function it_delete_limiting_the_targets()
    {
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $this->notifynderRepository->deleteLimit(1,5,"ASC");

        $allNotifications = $this->notificationModel->all();

        $this->assertCount(5,$allNotifications);
    }

    /** @test */
    public function it_get_notification_not_read_of_the_given_user()
    {
        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 1]);
        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $notificationNotRead = $this->notifynderRepository->getNotRead(1,null,false);
        $notificationAll = $this->notificationModel->all();

        $this->assertCount(5,$notificationNotRead);
        $this->assertCount(10,$notificationAll);
    }

    /** @test */
    public function it_get_notification_not_read_of_the_given_user_limiting_it()
    {
        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 1]);
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $notificationNotRead =$this->notifynderRepository->getNotRead(1,5,false);
        $notificationAll = $this->notificationModel->all();

        $this->assertCount(5,$notificationNotRead);
        $this->assertCount(15,$notificationAll);
    }

//    /** @test */
//    public function it_get_notification_not_read_of_the_given_user_paginating_it()
//    {
//        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 1]);
//        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);
//
//        $notificationNotRead = Notifynder::getNotRead(1,5,true);
//        $notificationAll = Notification::all();
//
//        $this->assertCount(5,$notificationNotRead);
//        $this->assertCount(15,$notificationAll);
//    }

    /** @test */
    public function it_get_all_notification_of_the_given_entity()
    {
        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 1]);
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $notificationGetAll = $this->notifynderRepository->getAll(1);
        $notificationAll = $this->notificationModel->all();

        $this->assertCount(15,$notificationGetAll);
        $this->assertCount(15,$notificationAll);
        $this->assertEquals(0,$notificationGetAll[0]->read);
    }

    /** @test */
    public function it_get_all_notification_of_the_given_entity_limiting_it()
    {
        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 1]);
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $notificationGetAll = $this->notifynderRepository->getAll(1,10);
        $notificationAll = $this->notificationModel->all();

        $this->assertCount(10,$notificationGetAll);
        $this->assertCount(15,$notificationAll);
        $this->assertEquals(0,$notificationGetAll[0]->read);
    }

//    /** @test */
//    public function it_get_all_notification_of_the_given_entity_paginating_it()
//    {
//        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 1]);
//        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);
//
//        $notificationGetAll = Notifynder::getAll(1,10,true);
//        $notificationAll = Notification::all();
//
//        $this->assertCount(10,$notificationGetAll);
//        $this->assertCount(15,$notificationAll);
//        $this->assertEquals(0,$notificationGetAll[0]->read);
//    }

    /** @test */
    public function it_count_notification_not_read()
    {
        TestDummy::times(5)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 1]);
        TestDummy::times(10)->create('Fenos\Notifynder\Models\Notification',['to_id' => 1, 'read' => 0]);

        $notificationCount = $this->notifynderRepository->countNotRead(1);
        $notificationAll = $this->notificationModel->all();

        $this->assertCount(15,$notificationAll);
        $this->assertEquals(10,$notificationCount->notRead);
    }
}