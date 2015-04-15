<?php

namespace spec\Fenos\Notifynder\Parsers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotifynderParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Parsers\NotifynderParser');
    }

    /** @test */
    function it_should_replace_special_values_with_an_associative_array()
    {
        $extra = ['hello' => 'world'];

        $notification = [
            'body' => [
                'text' => 'Hi jhon hello {extra.hello}'
            ],
            'extra' => json_encode($extra)
        ];

        $this->parse($notification)->shouldReturn('Hi jhon hello world');
    }

    /** @test */
    function it_replace_from_values_relations()
    {
        $notification = [
            'body' => [
                'text' => 'Hi {to.username} hello'
            ],
            'to' => [
                'username' => 'jhon',
            ],
            'extra' => null
        ];

        $this->parse($notification)->shouldReturn('Hi jhon hello');
    }

    /** @test */
    function it_replace_both_in_a_string()
    {
        $extra = ['hello' => 'world'];

        $notification = [
            'body' => [
                'text' => 'Hi {to.username} hello {extra.hello}'
            ],
            'to' => [
                'username' => 'jhon',
            ],
            'extra' => json_encode($extra)
        ];

        $this->parse($notification)->shouldReturn('Hi jhon hello world');
    }
}
