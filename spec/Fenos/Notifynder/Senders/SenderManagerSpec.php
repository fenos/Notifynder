<?php

namespace spec\Fenos\Notifynder\Senders;

use BadMethodCallException;
use Fenos\Notifynder\Contracts\DefaultSender;
use Fenos\Notifynder\Contracts\StoreNotification;
use Fenos\Notifynder\Senders\SenderFactory;
use Illuminate\Contracts\Foundation\Application;
use PhpSpec\ObjectBehavior;

class SenderManagerSpec extends ObjectBehavior
{
    public function let(SenderFactory $senderFactory, StoreNotification $storeNotification, Application $application)
    {
        $this->beConstructedWith($senderFactory, $storeNotification, $application);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Senders\SenderManager');
    }

    /** @test */
    public function it_send_now_a_notification(SenderFactory $senderFactory,
                                        DefaultSender $sender,
                                        StoreNotification $storeNotification)
    {
        $notifications = [];
        $category = 1;

        $senderFactory->getSender($notifications, $category)->shouldBeCalled()
                ->willReturn($sender);

        $sender->send($storeNotification)->shouldBeCalled()
                ->willReturn(1);

        $this->sendNow($notifications, $category)->shouldReturn(1);
    }

    /** @test */
    public function it_send_one_notification(SenderFactory $senderFactory,
                                      DefaultSender $sender,
                                      StoreNotification $storeNotification)
    {
        $notifications = [];
        $category = 1;

        $senderFactory->sendSingle($notifications, $category)->shouldBeCalled()
            ->willReturn($sender);

        $sender->send($storeNotification, $category)->shouldBeCalled()
            ->willReturn(1);

        $this->sendOne($notifications, $category)->shouldReturn(1);
    }

    /** @test */
    public function it_send_multiple_notification(SenderFactory $senderFactory,
                                      DefaultSender $sender,
                                      StoreNotification $storeNotification)
    {
        $notifications = [];

        $senderFactory->sendMultiple($notifications)->shouldBeCalled()
            ->willReturn($sender);

        $sender->send($storeNotification)->shouldBeCalled()
            ->willReturn(1);

        $this->sendMultiple($notifications)->shouldReturn(1);
    }

    /** @test */
    public function it_call_an_extended_method(SenderFactory $senderFactory,
                                        DefaultSender $sender,
                                        StoreNotification $storeNotification)
    {
        $notifications = [];

        $this->extend('sendExtended', function ($app) use ($sender) {
            return new TestExtender();
        });

        $this->sendExtended($notifications)->shouldReturn([]);
    }

    /** @test */
    public function it_try_to_call_an_inexistent_extended_method()
    {
        $this->shouldThrow(BadMethodCallException::class)->during('NotExistingExtender', []);
    }
}

/*
|--------------------------------------------------------------------------
| Extended class
|--------------------------------------------------------------------------
| Test Extender sender class
--------------------------------------------------------------------------*/

class TestExtender implements DefaultSender
{
    /**
     * Send Single notification.
     *
     * @param StoreNotification $sender
     * @return mixed
     */
    public function send(StoreNotification $sender)
    {
        return [];
    }
}
