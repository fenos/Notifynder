<?php

use Mockery as m;
use Fenos\Notifynder\Handler\NotifynderHandler;

class Handler
{
    public function foo()
    {
        return true;
    }
}

/**
* Test NofitynderParseTest Class
*/
class NofitynderHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
    * @var
    */
    protected $notifynderHandler;

    /**
    * @var mock app
    */
    protected $app;

    public function setUp()
    {
        $this->notifynderHandler = new NotifynderHandler(
            $this->app = m::mock('Illuminate\Foundation\Application')
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

    public function test_listen_notificiations()
    {
        $result = $this->notifynderHandler->listen(['key' => 'handler', 'handler' => 'stdClass']);
        $arrayEquals = $notifynderHandler['handler'] = 'stdClass';

        $this->assertEquals($arrayEquals,$result);
    }

    public function test_fire_method_when_it_invoked()
    {
        $notifynderHandler = m::mock('Fenos\Notifynder\Handler\NotifynderHandler[getFunction]',[$this->app]);

         // I set up a default value for our test using reflection
        $listener['handler'] = "Handler@foo";
        $reflection = new \ReflectionClass($notifynderHandler);
        $reflection_property = $reflection->getProperty('listener');
        $reflection_property->setAccessible(true);

        $reflection_property->setValue($notifynderHandler, $listener);

        $notifynderHandler->shouldReceive('getFunction')
                        ->once()
                        ->with('Handler@foo')
                        ->andReturn(['Handler','foo']);

        $this->app->shouldReceive('make')
                                ->once()
                                ->andReturn(new Handler);


        $result = $notifynderHandler->fire(m::mock('Fenos\Notifynder\Notifynder'), 'handler',['values' => 1, 'use' => function(){
            return true;
        }]);

        $this->assertTrue($result);

    }

    public function test_get_class_and_function_from_a_string()
    {
        $result = $this->notifynderHandler->getFunction('Handler@foo');

        $expect = ['Handler','foo'];

        $this->assertEquals($expect,$result);
    }
}
