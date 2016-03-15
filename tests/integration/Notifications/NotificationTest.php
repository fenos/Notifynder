<?php
use Fenos\Notifynder\Notifications\NotificationManager;

/**
 * Class NotificationTest
 */
class NotificationTest extends TestCaseDB {

    use CreateModels;

    /**
     * @var NotificationManager
     */
    protected $notification;

    /**
     * @var int
     */
    protected $multiNotificationsNumber = 10;

    /**
     * @var array
     */
    protected $to = [
        'id' => 1,
        'type' => 'Fenos\Tests\Models\User'
    ];

    /**
     * Set Up Test
     */
    public function setUp()
    {
        parent::setUp();
        $this->notification = app('notifynder.notification');
    }

    /** @test */
    function it_retrieve_notification_with_parsed_body()
    {
        $extraValues = json_encode(['look' => 'Amazing']);
        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues,'category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id);

        $bodyParsed = 'parse this Amazing value';
        $this->assertEquals($bodyParsed,$notifications[0]->text);
    }

    /** @test */
    function it_retrieve_notification_by_limiting_the_number()
    {
        $this->createMultipleNotifications();

        // set polymorphic to true
        app('config')->set('notifynder.polymorphic',true);

        $notification = $this->createNotification(['extra' => 'Amazing']);
        $this->createMultipleNotifications(['to_id' => $notification->to_id]);

        $notifications = $this->notification->entity($this->to['type'])
            ->getAll($notification->to->id,1);

        $this->assertCount(1,$notifications);
    }

    /** @test */
    function it_retrieve_notification_by_paginating_the_number()
    {
        app('config')->set('notifynder.polymorphic',false);
        $extraValues = json_encode(['look' => 'Amazing']);

        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues,'category_id' => $category->id]);
        $this->createMultipleNotifications(['to_id' => $notification->to_id]);

        $notifications = $this->notification->getNotRead($notification->to->id,5,1);

        $this->assertCount(5,$notifications);
    }

    /**
     * It will query adding the filter scope
     * on of the category by name
     *
     * @test
     */
    function it_will_query_for_notification_by_category_name()
    {
        app('config')->set('notifynder.polymorphic',false);
        $this->createMultipleNotifications();
        $category = $this->createCategory(['text' => 'parse this {extra.look} value','name' => 'text']);
        $this->createMultipleNotifications(['category_id' => $category->id]);

        $user = new \Fenos\Tests\Models\User(['id' => $this->to['id']]);

        $notificationByCategory = $user->getNotifications(false,false,'desc', function($query) use ($category) {
           $query->byCategory('text');
        });

        $this->assertCount(10,$notificationByCategory);
    }
}