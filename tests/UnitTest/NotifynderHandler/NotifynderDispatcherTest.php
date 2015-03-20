<?php
/**
 * Created by Fabrizio Fenoglio.
 */
use Fenos\Notifynder\Handler\NotifynderDispatcher;
use Mockery as m;

class NotifynderDispatcherMock extends NotifynderDispatcher
{

    public function postAdd()
    {
        return "addPost";
    }
}

class NotifynderDispatcherTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var NotifynderDispatcherMock
     */
    protected $notifynderDispatcher;

    /**
     * @var Illuminate\Events\Dispatcher
     */
    protected $event;

    public function setUp()
    {
        $this->notifynderDispatcher = new NotifynderDispatcherMock();
    }

    /**
     * Tear down function for all tests
     *
     */
    public function teardown()
    {
        m::close();
    }

    /** @test */
    public function it_handle_the_method_to_call_the_event_inkoed()
    {
        $mockDispatcher = m::mock('NotifynderDispatcherMock[getEventName,listenerIsRegistered]');

        $eventName = "user.post.add";
        $categoryName = "test";
        $notifynder = m::mock('Fenos\Notifynder\Notifynder');

        $mockDispatcher->shouldReceive('getEventName')
             ->once()
             ->with($eventName)
             ->andReturn('postAdd');

        $mockDispatcher->shouldReceive('listenerIsRegistered')
             ->once()
             ->with('postAdd')
             ->andReturn(true);

        $result = $mockDispatcher->handle(['eventName' => $eventName], $categoryName, $notifynder);

        $this->assertEquals('addPost', $result);
    }

    /** @test */
    public function it_check_if_the_method_exists_on_the_class()
    {
        $result = $this->notifynderDispatcher->listenerIsRegistered('postAdd');

        $this->assertTrue($result);
    }

    /** @test */
    public function it_get_event_name_transforming_from_the_key()
    {
        $nameEvent = "user.post.add";

        $result = $this->notifynderDispatcher->getEventName($nameEvent);

        $this->assertEquals('postAdd', $result);
    }
}
