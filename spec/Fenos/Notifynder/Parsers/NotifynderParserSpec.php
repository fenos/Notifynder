<?php

namespace spec\Fenos\Notifynder\Parsers;

use Fenos\Notifynder\Exceptions\ExtraParamsException;
use Fenos\Notifynder\Parsers\NotifynderParser;
use PhpSpec\ObjectBehavior;

class NotifynderParserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Parsers\NotifynderParser');
    }

    /** @test */
    public function it_should_replace_special_values_with_an_associative_array()
    {
        $extra = ['hello' => 'world'];

        $notification = [
            'body' => [
                'text' => 'Hi jhon hello {extra.hello}',
            ],
            'extra' => json_encode($extra),
        ];

        $this->parse($notification)->shouldReturn('Hi jhon hello world');
    }

    /** @test */
    public function it_replace_from_values_relations()
    {
        $notification = [
            'body' => [
                'text' => 'Hi {to.username} hello',
            ],
            'to' => [
                'username' => 'jhon',
            ],
            'extra' => null,
        ];

        $this->parse($notification)->shouldReturn('Hi jhon hello');
    }

    /** @test */
    public function it_replace_both_in_a_string()
    {
        $extra = ['hello' => 'world'];

        $notification = [
            'body' => [
                'text' => 'Hi {to.username} hello {extra.hello}',
            ],
            'to' => [
                'username' => 'jhon',
            ],
            'extra' => json_encode($extra),
        ];

        $this->parse($notification)->shouldReturn('Hi jhon hello world');
    }

    /** @test */
    public function it_will_remove_extra_markup_if_extra_value_is_not_provided()
    {
        $extra = [];

        $notification = [
            'body' => [
                'text' => 'Hi {to.username} hello {extra.hello}',
            ],
            'to' => [
                'username' => 'jhon',
            ],
            'extra' => json_encode($extra),
        ];

        // note the space, TODO: shall i remove it?
        $this->parse($notification)->shouldReturn('Hi jhon hello ');
    }

    /** @test */
    public function it_will_throw_exception_when_strict_extra_is_enabled()
    {
        $extra = null;

        $notification = [
            'body' => [
                'text' => 'Hi {to.username} hello {extra.hello}',
            ],
            'to' => [
                'username' => 'jhon',
            ],
            'extra' => json_encode($extra),
        ];

        NotifynderParser::setStrictExtra(true);

        $this->shouldThrow(ExtraParamsException::class)
            ->during('parse', [$notification]);
    }

    /** @test */
    public function it_will_parse_4_extra_params()
    {
        $extra = [
            'name' => 'fabri',
            'username' => 'fenos',
            'status' => 'active',
            'prof' => 'dev',
        ];

        $text = 'Hi {extra.name}, your username is: {extra.username} your status: {extra.status} your profession: {extra.prof}';
        $notification = [
            'body' => [
                'text' => $text,
            ],
            'extra' => json_encode($extra),
        ];

        $parsedText = "Hi {$extra['name']}, your username is: {$extra['username']} your status: {$extra['status']} your profession: {$extra['prof']}";
        $this->parse($notification)->shouldReturn($parsedText);
    }
}
