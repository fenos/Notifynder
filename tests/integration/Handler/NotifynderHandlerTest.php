<?php

use Fenos\Notifynder\Builder\NotifynderBuilder;
use Fenos\Notifynder\Handler\NotifynderEvent;
use Fenos\Notifynder\Handler\NotifynderHandler;
use Fenos\Notifynder\NotifynderManager;
use Fenos\Tests\Models\User;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class NotifynderHandlerTest.
 */
class NotifynderHandlerTest extends TestCaseDB
{
    use CreateModels;

    /**
     * @var NotifynderManager
     */
    protected $dispatcher;

    /**
     * Test listeners.
     *
     * @var array
     */
    protected $listeners = [
        'notify.*' => 'NotifyUserTest',
    ];

    /**
     * User to.
     *
     * @var User
     */
    protected $to;

    /**
     * @var User
     */
    protected $from;

    /**
     * @var Dispatcher
     */
    protected $laravelDispatcher;

    /**
     * Listen test listeners.
     */
    public function setUp()
    {
        parent::setUp();

        $this->dispatcher = app('notifynder');
        $this->laravelDispatcher = app('events');

        // Boot Listeners
        $this->dispatcher->bootListeners($this->listeners);

        // Create Users
        $this->to = $this->createUser();
        $this->from = $this->createUser();

        // Create Category
        $this->createCategory([
            'name' => 'activation',
        ]);

        $this->createCategory([
            'name' => 'confirmation',
        ]);
    }

    /** @test */
    public function it_fire_an_event_sending_a_specific_notification_from_the_handler()
    {
        $this->dispatcher->fire('notify@userActivated', 'activation');

        $notification = \Fenos\Notifynder\Models\Notification::all();

        $this->assertCount(1, $notification);
    }

    /** @test */
    public function it_fire_an_event_sending_multiple_notifications()
    {
        $this->dispatcher->fire('notify@userMultiple', 'activation');

        $notification = \Fenos\Notifynder\Models\Notification::all();

        $this->assertCount(2, $notification);
    }

    /** @test */
    public function it_delete_2_notification_to_be_sent_trought_the_handler()
    {
        $this->dispatcher->delegate([
            'activation'    => 'notify@userActivated',
            'confirmation'  => 'notify@userMultiple',
        ]);

        $notification = \Fenos\Notifynder\Models\Notification::all();

        $this->assertCount(3, $notification);
    }

    /** @test */
    public function it_trigger_an_handler_using_native_laravel_dispatcher()
    {
        $testListener = [
            NotifyEvent::class => [
                NotifyUserTest::class,
            ],
        ];

        // Listen for events as the laravel way
        foreach ($testListener as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->laravelDispatcher->listen($event, $listener);
            }
        }

        $notification = $this->laravelDispatcher->fire(
            new NotifyEvent(new NotifynderEvent('userActivated'))
        );

        $this->assertEquals('hello', $notification[0]->url);
    }
}

/*
|--------------------------------------------------------------------------
| NotifyUserTest: Example of Handler
|--------------------------------------------------------------------------
| NotifyUserTest Class is an handler to test the implementation against it
--------------------------------------------------------------------------*/

/**
 * Class NotifyUserTest.
 */
class NotifyUserTest extends NotifynderHandler
{
    /**
     * Test trigger one notification.
     *
     * @param NotifynderEvent $event
     * @param NotifynderManager      $notifynder
     * @return mixed
     * @throws \Fenos\Notifynder\Exceptions\NotificationBuilderException
     */
    public function userActivated(NotifynderEvent $event, NotifynderManager $notifynder)
    {
        return $notifynder->builder()
                          ->category('activation')
                          ->url('hello')
                          ->from(1)
                          ->to(2);
    }

    /**
     * Test send multiple notifications from
     * the handler.
     *
     * @param NotifynderEvent $event
     * @param NotifynderManager      $notifynder
     * @return $this
     */
    public function userMultiple(NotifynderEvent $event, NotifynderManager $notifynder)
    {
        // Retrieve users
        $users = [1, 2];

        return $notifynder->builder()->loop($users, function (NotifynderBuilder $builder, $value, $key) {
            return $builder->category('activation')
                ->url('hello')
                ->from(1)
                ->to($value);
        });
    }
}
