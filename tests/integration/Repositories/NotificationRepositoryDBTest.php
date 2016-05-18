<?php

use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Notifications\NotificationRepository;
use Laracasts\TestDummy\Factory;

class NotificationRepositoryDBTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var NotificationRepository
     */
    protected $notificationRepo;

    /**
     * @var array
     */
    protected $to = [
        'id' => 1,
        'type' => 'User',
    ];

    /**
     * @var int
     */
    protected $multiNotificationsNumber = 10;

    /**
     * @var int
     */
    protected $to_id = 1;

    /**
     * SetUp Tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->notificationRepo = app('notifynder.notification.repository');
    }

    /** @test */
    public function it_find_a_notification_by_id()
    {
        $notificationToSearch = $this->createNotification();

        $notification = $this->notificationRepo->find($notificationToSearch->id);

        $this->assertEquals($notificationToSearch->id, $notification->id);
    }

    /** @test */
    public function it_send_a_single_notification()
    {
        $notificationToSend = $this->buildNotification();

        $notification = $this->notificationRepo->storeSingle($notificationToSend);

        $this->assertEquals($notificationToSend['to_id'], $notification->to_id);
        $this->assertEquals($notificationToSend['to_type'], $notification->to_type);
    }

    /** @test
     * @group fails
     */
    public function it_send_multiple_notification()
    {
        $notificationsToSend[0] = $this->buildNotification();
        $notificationsToSend[1] = $this->buildNotification();
        //dd($notificationsToSend);
        $storeMultipleNotificaations = $this->notificationRepo->storeMultiple($notificationsToSend);

        $notifications = Notification::all();

        $this->assertCount(2, $notifications);
        $this->assertEquals(2, $storeMultipleNotificaations);
    }

    /** @test */
    public function it_read_one_notification_by_id()
    {
        $notificationToRead = $this->createNotification();

        $notificationRead = $this->notificationRepo->readOne($notificationToRead);

        $this->assertEquals(1, $notificationRead->read);
    }

    /** @test */
    public function it_read_limit_the_number_of_notifications_of_the_given_entity()
    {
        $this->createMultipleNotifications();

        $readFive = $this->notificationRepo->readLimit(
            $this->to['id'], $this->to['type'], 5, 'asc'
        );

        $notificationsRead = Notification::whereRead(1)->get();

        $this->assertEquals(5, $readFive);
        $this->assertCount(5, $notificationsRead);
    }

    /** @test */
    public function it_read_all_the_notifications_of_the_given_entity()
    {
        $this->createMultipleNotifications();

        $notificationRead = $this->notificationRepo->readAll(
            $this->to['id'], $this->to['type']
        );

        $this->assertEquals(10, $notificationRead);
    }

    /** @test */
    public function it_delete_a_notification_by_id()
    {
        $notificationToDelete = $this->createNotification();

        $deleted = $this->notificationRepo->delete($notificationToDelete->id);

        $this->assertEquals(1, $deleted);
        $this->assertCount(0, Notification::all());
    }

    /** @test */
    public function it_delete_all_the_notification_of_the_given_entity()
    {
        $this->createMultipleNotifications();

        $deleted = $this->notificationRepo->deleteAll(
            $this->to['id'], $this->to['type']
        );

        $this->assertEquals(10, $deleted);
        $this->assertCount(0, Notification::all());
    }

    /** @test */
    public function it_delete_notifications_limit_the_number_of_the_given_entity()
    {
        $this->createMultipleNotifications();

        $notificationsDeleted = $this->notificationRepo->deleteLimit(
            $this->to['id'], $this->to['type'], 5, 'asc'
        );

        $this->assertEquals(5, $notificationsDeleted);
        $this->assertCount(5, Notification::all());
    }

    /** @test */
    public function it_count_notification_not_read()
    {
        $this->createMultipleNotifications();

        $countNotRead = $this->notificationRepo->countNotRead(
            $this->to['id'], $this->to['type']
        );

        $this->assertEquals($this->multiNotificationsNumber, $countNotRead);
    }

    /** @test */
    public function it_delete_all_notification_by_category()
    {
        $category = $this->createCategory(['name' => 'test']);

        $this->createNotification(['category_id' => $category->id]);
        $this->createNotification(['category_id' => $category->id]);
        $this->createNotification();

        $this->notificationRepo->deleteByCategory($category->name);

        $this->assertCount(1, Notification::all());
    }

    /** @test */
    public function it_delete_all_notification_expired_by_category_name()
    {
        $category = $this->createCategory(['name' => 'test']);

        $this->createNotification([
            'category_id' => $category->id,
            'expire_time' => Carbon\Carbon::now()->subDays(1),
        ]);

        $this->createNotification([
            'category_id' => $category->id,
            'expire_time' => Carbon\Carbon::now()->subDays(1),
        ]);

        $this->createNotification([
            'category_id' => $category->id,
            'expire_time' => Carbon\Carbon::now()->subDays(1),
        ]);

        $this->createNotification([
            'category_id' => $category->id,
            'expire_time' => Carbon\Carbon::now()->addDays(1),
        ]);

        $this->notificationRepo->deleteByCategory($category->name, true);

        $this->assertCount(1, Notification::all());
    }

    /** @test */
    public function it_get_the_last_notificiation_sent()
    {
        $category = $this->createCategory(['name' => 'test']);

        $this->createNotification([
            'category_id' => $category->id,
            'url' => 'first',
            'to_id' => 1,
            'created_at' => Carbon\Carbon::now()->addDay(1),
        ]);

        $this->createNotification([
            'category_id' => $category->id,
            'url' => 'second',
            'to_id' => 1,
            'created_at' => Carbon\Carbon::now()->addDay(2),
        ]);

        $notification = $this->notificationRepo->getLastNotification(1, null);

        $this->assertEquals('second', $notification->url);
    }

    /** @test */
    public function it_get_the_last_notificiation_sent_by_category()
    {
        $category1 = $this->createCategory(['name' => 'test']);
        $category2 = $this->createCategory(['name' => 'test2']);

        $this->createNotification([
            'category_id' => $category1->id,
            'url' => 'first',
            'to_id' => 1,
            'created_at' => Carbon\Carbon::now()->addDay(1),
        ]);

        $this->createNotification([
            'category_id' => $category1->id,
            'url' => 'second',
            'to_id' => 1,
            'created_at' => Carbon\Carbon::now()->addDay(2),
        ]);

        $this->createNotification([
            'category_id' => $category2->id,
            'url' => 'third',
            'to_id' => 1,
            'created_at' => Carbon\Carbon::now()->addDay(3),
        ]);

        $notificationByName = $this->notificationRepo->getLastNotificationByCategory('test', 1, null);
        $notificationById = $this->notificationRepo->getLastNotificationByCategory($category1->id, 1, null);

        $this->assertEquals('second', $notificationByName->url);
        $this->assertEquals('second', $notificationById->url);
    }

    /**
     * Shortcut to build a new notification.
     *
     * @param array $data
     * @return array
     */
    protected function buildNotification(array $data = [])
    {
        $notification = Factory::build(Notification::class, $data)->toArray();
        $notification['extra'] = json_encode($notification['extra']);

        return $notification;
    }
}
