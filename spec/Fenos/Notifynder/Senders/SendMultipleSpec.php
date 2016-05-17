<?php

namespace spec\Fenos\Notifynder\Senders;

use Fenos\Notifynder\Contracts\StoreNotification;
use PhpSpec\ObjectBehavior;

class SendMultipleSpec extends ObjectBehavior
{
    public function let()
    {
        $notifications = [];
        $this->beConstructedWith($notifications);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Senders\SendMultiple');
    }

    /** @test */
    public function it_send_multiple_notification(StoreNotification $storeNotification)
    {
        $multiple = [
        ];

        $storeNotification->storeMultiple($multiple)->shouldBeCalled()
                ->willReturn(1);

        $this->send($storeNotification)->shouldReturn(1);
    }
}
