<?php

use Fenos\Notifynder\Builder\NotifynderBuilder;
use Fenos\Notifynder\Contracts\NotifynderGroup;
use Fenos\Notifynder\Contracts\NotifynderSender;
use Fenos\Notifynder\Models\Notification;

/**
 * Class SendersTest.
 */
class SendersTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var NotifynderSender
     */
    protected $senders;

    /**
     * @var NotifynderBuilder
     */
    protected $builder;

    /**
     * @var NotifynderGroup
     */
    protected $group;

    /**
     * Set up the sender.
     */
    public function setUp()
    {
        parent::setUp();

        // Set the model from the config
        app('config')->set(
            'notifynder.notification_model',
            'Fenos\Notifynder\Models\Notification'
        );

        $this->senders = app('notifynder.sender');
        $this->builder = app('notifynder.builder');
        $this->group = app('notifynder.group');
    }

    /** @test */
    public function it_send_now_a_single_notification()
    {
        $category_name = 'my.category';
        $this->createCategory(['name' => $category_name]);

        $singleNotification = $this->builder->category($category_name)
                                    ->to(1)
                                    ->from(2)
                                    ->url('www.notifynder.io')
                                    ->toArray();

        // Send Single
        $this->senders->sendNow($singleNotification);

        $stackIds = Notification::lists('stack_id');
        if ($stackIds instanceof \Illuminate\Support\Collection) {
            $stackIds = $stackIds->toArray();
        }
        $stackIds = array_unique($stackIds);

        $this->assertCount(1, Notification::all());
        $this->assertCount(1, $stackIds);
        $this->assertEquals([null], $stackIds);
    }

    /** @test */
    public function it_send_now_a_mutiple_notification()
    {
        $category_name = 'my.category';
        $this->createCategory(['name' => $category_name]);

        $user_ids = [1, 2];

        $sendMultiple = $this->builder->loop($user_ids,
            function (NotifynderBuilder $builder, $value) use ($category_name) {
                return $builder->category($category_name)
                    ->to($value)
                    ->from(2)
                    ->url('www.notifynder.io')
                    ->toArray();
            });

        // Send Single
        $this->senders->sendNow($sendMultiple);

        $stackIds = Notification::lists('stack_id');
        if ($stackIds instanceof \Illuminate\Support\Collection) {
            $stackIds = $stackIds->toArray();
        }
        $stackIds = array_unique($stackIds);

        $this->assertCount(2, Notification::all());
        $this->assertCount(1, $stackIds);
        $this->assertEquals([1], $stackIds);
    }

    /** @test */
    public function it_send_a_group_of_notification()
    {
        $group = $this->createGroup(['name' => 'mygroud']);
        $category1 = $this->createCategory();
        $category2 = $this->createCategory();
        $category3 = $this->createCategory();

        $this->group->addMultipleCategoriesToGroup($group->name,
            $category1->name,
            $category2->name,
            $category3->name
        );

        $this->senders->sendGroup($group->name, [
            'from_id' => 1,
            'to_id' => 2,
            'url' => 'www.notifynder.io',
        ]);

        $this->assertCount(3, Notification::all());
    }

    /** @test */
    public function it_send_with_an_custom_sender()
    {
        $this->senders->extend('sendCustom', function ($notification, $app) {
            return new CustomDefaultSender($notification, $app->make('notifynder'));
        });

        $category_name = 'my.category';
        $this->createCategory(['name' => $category_name]);

        $singleNotification = $this->builder->category($category_name)
                ->to(1)
                ->from(2)
                ->url('www.notifynder.io')
                ->toArray();

        $this->senders->sendCustom($singleNotification);

        $this->assertCount(1, Notification::all());
    }

    /** @test */
    public function it_send_multiple_with_an_custom_sender()
    {
        $this->senders->extend('sendCustom', function ($notification, $app) {
            return new CustomDefaultSender($notification, $app->make('notifynder'));
        });

        $category_name = 'my.category';
        $this->createCategory(['name' => $category_name]);

        $multipleNotifications = [];
        $multipleNotifications[] = $this->builder->category($category_name)
            ->to(1)
            ->from(2)
            ->url('www.notifynder.io')
            ->toArray();
        $multipleNotifications[] = $this->builder->category($category_name)
            ->to(2)
            ->from(1)
            ->url('notifynder.com')
            ->toArray();

        $this->senders->sendCustom($multipleNotifications);

        $this->assertCount(2, Notification::all());
    }
}
