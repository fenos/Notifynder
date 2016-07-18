<?php

use Fenos\Notifynder\Models\Notification;
use Fenos\Tests\Models\User;
use Laracasts\TestDummy\Factory;

/**
 * Class NotificationTest.
 */
class NotifableTraitTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var int
     */
    protected $multiNotificationsNumber = 10;

    /**
     * @var array
     */
    protected $to = [
        'id' => 1,
        'type' => 'Fenos\Tests\Models\User',
    ];

    /**
     * @var Notification
     */
    protected $notification;

    /**
     * @var User
     */
    protected $user;

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        parent::setUp();
        $this->notification = app('notifynder.notification');
        $this->user = Factory::create(User::class);
    }

    /**
     * @test
     */
    public function it_count_notification_not_read()
    {
        $this->createMultipleNotifications(['read' => 1]);

        $count = $this->user->countNotificationsNotRead();

        $this->assertEquals(0, $count);
    }

    /**
     * It read all notifications.
     *
     * @method readLimitNotifications
     * @test
     */
    public function it_real_all_notifications()
    {
        $this->createMultipleNotifications();

        $read = $this->user->readAllNotifications();

        $this->assertEquals(10, $read);
    }

    /**
     * It read limiting amount the of
     * notifications.
     *
     * @method readLimitNotifications
     * @test
     */
    public function it_read_a_limit_of_notifications()
    {
        $this->createMultipleNotifications();

        $read = $this->user->readLimitNotifications(6);

        $this->assertEquals(6, $read);
    }

    /**
     * It delete limiting the amount of
     * notifications.
     *
     * @method deleteLimitNotifications
     * @test
     */
    public function it_delete_limit_notifications()
    {
        $this->createMultipleNotifications();

        $deleted = $this->user->deleteLimitNotifications(4);

        $this->assertEquals(4, $deleted);
    }

    /**
     * It delete all notifications.
     *
     * @method deleteAllNotifications
     * @test
     */
    public function it_delete_all_notifications()
    {
        $this->createMultipleNotifications();

        $deleted = $this->user->deleteAllNotifications();

        $this->assertEquals($this->multiNotificationsNumber, $deleted);
    }

    /**
     * Get notifications unread.
     *
     * @method
     * @test
     */
    public function it_get_notifications_not_read()
    {
        // 20 total
        $this->createMultipleNotifications();
        $this->createMultipleNotifications();
        $this->user->readLimitNotifications(10); // 10 read

        $getNotificationNotRead = $this->user->getNotificationsNotRead();

        $this->assertCount(10, $getNotificationNotRead);
    }

    /**
     * Get all notifications.
     *
     * @method getNotifications
     * @test
     */
    public function it_get_all_notification_of_the_current_user()
    {
        $this->createMultipleNotifications();

        $notifications = $this->user->getNotifications();

        $this->assertCount(10, $notifications);
    }

    /**
     * get the last notification.
     *
     * @method getLastNotification
     * @test
     */
    public function it_get_last_notification()
    {
        $this->createMultipleNotifications();

        $lastNotification = $this->user->getLastNotification();

        $notification = Notification::orderBy('created_at', 'desc')->first();

        $this->assertEquals($notification->id, $lastNotification->id);
    }

    /**
     * @test
     */
    public function it_get_paginated_notifications_of_the_current_user()
    {
        $this->createMultipleNotifications();
        $this->createMultipleNotifications();
        $this->createMultipleNotifications();

        $notifications = $this->user->getNotifications(5, true);

        $this->assertSame(3 * $this->multiNotificationsNumber, $notifications->total());
        $this->assertCount(5, $notifications);
    }
}
