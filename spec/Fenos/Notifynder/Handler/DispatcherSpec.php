<?php

namespace spec\Fenos\Notifynder\Handler;

use Fenos\Notifynder\Handler\NotifynderEvent;
use Fenos\Notifynder\NotifynderManager;
use Illuminate\Contracts\Events\Dispatcher;
use PhpSpec\ObjectBehavior;

class DispatcherSpec extends ObjectBehavior
{
    public function let(Dispatcher $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Handler\Dispatcher');
    }

    /** @test */
    public function it_fire_a_notifynder_event(Dispatcher $dispatcher, NotifynderManager $notifynder)
    {
        $key = 'event';
        $category = 'hello';
        $extraValues = [];
        $notifyEvent = 'Notifynder.'.$key;
        $notificationBuilt = [
            0 => ['notification'],
        ];

        $notifynderEvent = new NotifynderEvent($notifyEvent, $category, $extraValues);

        $dispatcher->fire($notifyEvent, [$notifynderEvent, $notifynder])->shouldBeCalled()
                    ->willReturn($notificationBuilt);

        $notifynder->send($notificationBuilt[0])->shouldBeCalled()
                    ->willReturn(1);

        $this->fire($notifynder, $key, $category, $extraValues)->shouldReturn(1);
    }

    /** @test */
    public function it_fire_a_notifynder_event_having_nothing_to_send(Dispatcher $dispatcher, NotifynderManager $notifynder)
    {
        $key = 'event';
        $category = 'hello';
        $extraValues = [];
        $notifyEvent = 'Notifynder.'.$key;

        $notifynderEvent = new NotifynderEvent($notifyEvent, $category, $extraValues);

        $dispatcher->fire($notifyEvent, [$notifynderEvent, $notifynder])->shouldBeCalled()
            ->willReturn(null);

        $this->fire($notifynder, $key, $category, $extraValues)
            ->shouldReturn(null);
    }
}
