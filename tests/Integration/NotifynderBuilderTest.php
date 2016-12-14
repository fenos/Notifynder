<?php

use Carbon\Carbon;
use Fenos\Notifynder\Builder\NotifynderBuilder;

/**
 * Class NotifynderBuilderTest.
 */
class NotifynderBuilderTest extends IntegrationDBTest
{
    /**
     * @var NotifynderBuilder
     */
    protected $notifynderBuilder;

    /**
     * Set UP.
     */
    public function setUp()
    {
        parent::setUp();
        $this->notifynderBuilder = new NotifynderBuilder(
            $this->app->make('notifynder.category')
        );
    }

    /**
     * @test
     * */
    public function it_build_a_notifynder_Array_Not_Polymorphic()
    {
        $build = $this->notifynderBuilder
            ->from(1)
            ->to(2)
            ->category(1)
            ->url('same')
            ->extra('hello')
            ->getArray();

        $arrayThatShouldBeBuilt = [
            'from_id'     => 1,
            'to_id'       => 2,
            'category_id' => 1,
            'url'         => 'same',
            'extra'       => 'hello',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];

        $this->assertEquals($build, $arrayThatShouldBeBuilt);
        $this->assertArrayHasKey('category_id', $build);
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\NotificationBuilderException
     * */
    public function it_build_a_notifynder_Array_missing_some_data()
    {
        $this->notifynderBuilder
            ->from(1)
            // missing to() id
            ->category(1)
            ->url('same')
            ->extra('hello')
            ->getArray();
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\NotificationBuilderException
     * */
    public function it_build_a_notifynder_multidimensional_Array_missing_some_data()
    {
        $data = [1, 2];

        $this->notifynderBuilder->loop($data, function ($builder, $key, $data) {
            return $builder->to('Team', $data)
                ->category(1)
                ->url('same')
                ->extra('hello');
        });
    }

    /**
     * @test
     * */
    public function it_build_a_notifynder_Array_Polymorphic()
    {
        $build = $this->notifynderBuilder
            ->from('User', 1)
            ->to('Team', 2)
            ->category(1)
            ->url('same')
            ->extra('hello')
            ->getArray();

        $arrayThatShouldBeBuilt = [
            'from_id'     => 1,
            'from_type'   => 'User',
            'to_id'       => 2,
            'to_type'     => 'Team',
            'category_id' => 1,
            'url'         => 'same',
            'extra'       => 'hello',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];

        $this->assertEquals($build, $arrayThatShouldBeBuilt);
    }

    /**
     * @test
     * */
    public function it_build_a_notifynder_Array_a_condition_given()
    {
        $build = $this->notifynderBuilder->raw(function ($builder) {
            if (1 == 1) {
                return $builder->from('User', 1)
                    ->to('Team', 2)
                    ->category(1)
                    ->url('same')
                    ->extra('hello');
            }
        });

        $arrayThatShouldBeBuilt = [
            'from_id'     => 1,
            'from_type'   => 'User',
            'to_id'       => 2,
            'to_type'     => 'Team',
            'category_id' => 1,
            'url'         => 'same',
            'extra'       => 'hello',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];

        $this->assertEquals($build, $arrayThatShouldBeBuilt);
    }

    /**
     * @test
     * */
    public function it_build_a_notifynder_multidimensional_Array_for_multiple_notifications()
    {
        $data = [1, 2];

        $build = $this->notifynderBuilder->loop($data, function ($builder, $key, $data) {
            return $builder->from('User', 1)
                ->to('Team', $data)
                ->category(1)
                ->url('same')
                ->extra('hello');
        });

        $arrayThatShouldBeBuilt = [
            [
                'from_id'     => 1,
                'from_type'   => 'User',
                'to_id'       => 1,
                'to_type'     => 'Team',
                'category_id' => 1,
                'url'         => 'same',
                'extra'       => 'hello',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'from_id'     => 1,
                'from_type'   => 'User',
                'to_id'       => 2,
                'to_type'     => 'Team',
                'category_id' => 1,
                'url'         => 'same',
                'extra'       => 'hello',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ];

        $this->assertEquals($build, $arrayThatShouldBeBuilt);
    }

    /**
     * @test
     * */
    public function it_build_a_notifynder_multidimensional_Array_for_multiple_notifications_given_a_condition()
    {
        $data = [1, 2, 3];

        $build = $this->notifynderBuilder->loop($data, function ($builder, $key, $data) {
            if ($data == 1) {
                return $builder->from('User', 1)
                    ->to('Team', $data)
                    ->category(1)
                    ->url('same')
                    ->extra('hello');
            }
        });

        $arrayThatShouldBeBuilt = [
            [
                'from_id'     => 1,
                'from_type'   => 'User',
                'to_id'       => 1,
                'to_type'     => 'Team',
                'category_id' => 1,
                'url'         => 'same',
                'extra'       => 'hello',
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ];

        $this->assertEquals($build, $arrayThatShouldBeBuilt);
    }
}
