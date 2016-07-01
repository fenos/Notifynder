<?php

use Fenos\Notifynder\Notifications\NotificationManager;

/**
 * Class NotificationTest.
 */
class NotificationTest extends TestCaseDB
{
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
        'type' => 'Fenos\Tests\Models\User',
    ];

    /**
     * Set Up Test.
     */
    public function setUp()
    {
        parent::setUp();
        $this->notification = app('notifynder.notification');
    }

    /** @test */
    public function it_retrieve_notification_with_parsed_body()
    {
        $extraValues = json_encode(['look' => 'Amazing']);
        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id);

        $bodyParsed = 'parse this Amazing value';
        $this->assertEquals($bodyParsed, $notifications[0]->text);
    }

    /** @test */
    public function it_retrieve_notification_by_limiting_the_number()
    {
        $this->createMultipleNotifications();

        // set polymorphic to true
        app('config')->set('notifynder.polymorphic', true);

        $notification = $this->createNotification(['extra' => 'Amazing']);
        $this->createMultipleNotifications(['to_id' => $notification->to_id]);

        $notifications = $this->notification->entity($this->to['type'])
            ->getAll($notification->to->id, 1);

        $this->assertCount(1, $notifications);
    }

    /** @test */
    public function it_retrieve_notification_by_disable_pagination()
    {
        app('config')->set('notifynder.polymorphic', false);
        $extraValues = json_encode(['look' => 'Amazing']);

        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);
        $this->createMultipleNotifications(['to_id' => $notification->to_id]);

        $notifications = $this->notification->getNotRead($notification->to->id, 10, false);

        $this->assertCount(10, $notifications);
        $this->assertInstanceOf(\Fenos\Notifynder\Models\NotifynderCollection::class, $notifications);
    }

    /** @test */
    public function it_retrieve_notification_by_paginating_with_bool()
    {
        app('config')->set('notifynder.polymorphic', false);
        $extraValues = json_encode(['look' => 'Amazing']);

        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);
        $this->createMultipleNotifications(['to_id' => $notification->to_id]);

        $notifications = $this->notification->getNotRead($notification->to->id, 5, true);

        $this->assertCount(5, $notifications);
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $notifications);
    }

    /** @test */
    public function it_retrieve_notification_by_paginating_the_number()
    {
        app('config')->set('notifynder.polymorphic', false);
        $extraValues = json_encode(['look' => 'Amazing']);

        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);
        $this->createMultipleNotifications(['to_id' => $notification->to_id]);

        $notifications = $this->notification->getNotRead($notification->to->id, 5, 1);

        $this->assertCount(5, $notifications);
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $notifications);
    }

    /**
     * It will query adding the filter scope
     * on of the category by name.
     *
     * @test
     */
    public function it_will_query_for_notification_by_category_name()
    {
        app('config')->set('notifynder.polymorphic', false);
        $this->createMultipleNotifications();
        $category = $this->createCategory(['text' => 'parse this {extra.look} value', 'name' => 'text']);
        $this->createMultipleNotifications(['category_id' => $category->id]);

        $user = new \Fenos\Tests\Models\User(['id' => $this->to['id']]);

        $notificationByCategory = $user->getNotifications(false, false, 'desc', function ($query) use ($category) {
            $query->byCategory('text');
        });

        $this->assertCount(10, $notificationByCategory);
    }

    /**
     * It will check that the fillable fields config option are
     * allowing to save the model when resolved trough the ioc.
     *
     * @test
     * @group f
     */
    public function it_will_check_the_fillable_fields_options_are_allowing_to_save_the_model()
    {
        app('config')->set('notifynder.additional_fields.fillable', [
            'icon_type',
        ]);

        $model = app(\Fenos\Notifynder\Models\Notification::class);
        $fillable = [
            'to_id', 'to_type', 'from_id', 'from_type', 'category_id', 'read', 'url', 'extra', 'expire_time', 'stack_id', 'icon_type',
        ];

        $this->assertEquals($fillable, $model->getFillable());
    }

    /** @test */
    public function it_retrieve_notification_with_parsed_body_and_multi_dots()
    {
        $extraValues = json_encode(['look' => 'Amazing', 'user' => ['last' => 'Doe', 'first' => 'John']]);
        $category = $this->createCategory(['text' => 'parse this {extra.look} value from {extra.user.first} {extra.user.last}']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id);

        $bodyParsed = 'parse this Amazing value from John Doe';
        $this->assertEquals($bodyParsed, $notifications[0]->text);
    }

    /** @test */
    public function it_retrieve_notification_with_parsed_body_and_multi_dots_with_objects()
    {
        $user = new \Fenos\Tests\Models\User(['id' => '1']);
        $object = json_decode(json_encode(['last' => 'Doe', 'first' => 'John']), false);

        $this->assertInstanceOf(\Fenos\Tests\Models\User::class, $user);
        $this->assertInstanceOf(stdClass::class, $object);

        $extraValues = json_encode(['look' => 'Amazing', 'user' => $user, 'object' => $object]);
        $category = $this->createCategory(['text' => 'parse this {extra.look} value from User#{extra.user.id} ({extra.object.first} {extra.object.last})']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id);

        $bodyParsed = 'parse this Amazing value from User#1 (John Doe)';
        $this->assertEquals($bodyParsed, $notifications[0]->text);
    }

    /** @test */
    public function it_retrieve_notification_with_extraparams_extra()
    {
        $extraValues = json_encode(['look' => 'Amazing']);
        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id);
        $extra = $notifications->first()->extra;

        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(\Fenos\Notifynder\Notifications\ExtraParams::class, $extra);
        $this->assertEquals('Amazing', $extra->look);
    }

    /** @test */
    public function it_retrieve_notifications_toarray()
    {
        $extraValues = json_encode(['look' => 'Amazing']);
        $category = $this->createCategory(['text' => 'parse this {extra.look} value']);

        $notification = $this->createNotification(['extra' => $extraValues, 'category_id' => $category->id]);

        $notifications = $this->notification->getNotRead($notification->to->id)->toArray();

        $this->assertInternalType('array', $notifications);
        $this->assertCount(1, $notifications);
        $this->assertInternalType('array', $notifications[0]);
        $this->assertArrayHasKey('extra', $notifications[0]);
        $this->assertInternalType('array', $notifications[0]['extra']);
        $this->assertEquals('Amazing', $notifications[0]['extra']['look']);
    }

    /** @test */
    public function it_retrieve_notifications_by_stack_id()
    {
        $text = 'stack body text';
        $category = $this->createCategory(['text' => $text]);

        $this->createMultipleNotifications(['category_id' => $category->id]);

        $notifications = \Fenos\Notifynder\Models\Notification::byStack(1)->get();

        foreach ($notifications as $notification) {
            $this->assertEquals($text, $notification->text);
            $this->assertEquals(1, $notification->stack_id);
            $this->assertTrue($notification->hasStack());
        }
    }
}
