<?php

namespace spec\Fenos\Notifynder\Parsers;

use Fenos\Notifynder\Models\Notification;
use Fenos\Notifynder\Models\NotificationCategory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotifynderParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Parsers\NotifynderParser');
    }

    /** @test */
    function it_parse_the_body_of_a_given_notification()
    {
        $notification = [
            'id' => 1,
            'extra' => 'HELLO',
            'body' => [
                'name' => 'test',
                'text' => 'translate this {extra} parameter'
            ]
        ];

        $translated = 'translate this HELLO parameter';

        $this->parse($notification)->shouldReturn($translated);
    }
}
