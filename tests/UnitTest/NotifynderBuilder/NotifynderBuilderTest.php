<?php

use Fenos\Notifynder\Builder\NotifynderBuilder;
use Mockery as m;

/**
 * Class NotifynderBuilderTest
 */
class NotifynderUnitBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var NotifynderBuilder
     */
    protected $notifynderBuilder;

    /**
     * @var \Fenos\Notifynder\Categories\NotifynderCategory
     */
    protected $mockCategory;

    /**
     *
     */
    public function setUp()
    {
        $this->notifynderBuilder = new NotifynderBuilder(
            $this->mockCategory = m::mock('Fenos\Notifynder\Categories\NotifynderCategory')
        );
    }

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_get_the_built_array_adding_the_timeStamp()
    {
        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[setDate,getPropertiesToArray,hasRequiredFields]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('setDate')
             ->once()
             ->with()
             ->andReturn(null);

        $arrayGenerated = ['arrayof data'];

        $mockBuilder->shouldReceive('getPropertiesToArray')
            ->once()
            ->with($mockBuilder)
            ->andReturn($arrayGenerated);

        $mockBuilder->shouldReceive('hasRequiredFields')
            ->once()
            ->with($arrayGenerated)
            ->andReturn(true);

        $result = $mockBuilder->getArray();

        $this->assertEquals($arrayGenerated, $result);
    }

    /**
     * @test
     * @expectedException \Fenos\Notifynder\Exceptions\NotificationBuilderException
     * */
    public function it_get_the_built_array_adding_the_timeStamp_but_it_missing_required_data()
    {
        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[setDate,getPropertiesToArray,hasRequiredFields]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('setDate')
            ->once()
            ->with()
            ->andReturn(null);

        $arrayGenerated = ['arrayof data'];

        $mockBuilder->shouldReceive('getPropertiesToArray')
            ->once()
            ->with($mockBuilder)
            ->andReturn($arrayGenerated);

        $mockBuilder->shouldReceive('hasRequiredFields')
            ->once()
            ->with($arrayGenerated)
            ->andReturn(false);

        $mockBuilder->getArray();
    }

    /** @test */
    public function it_set_the_FROM_entity_data_in_the_property()
    {
        $jhon = 1;

        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[setEntityAction]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('setEntityAction')
             ->once()
             ->with([0 => $jhon], 'from')
             ->andReturn(['from' => 'jhon']);

        $result = $mockBuilder->from($jhon);

        $this->assertInstanceOf('Fenos\Notifynder\Builder\NotifynderBuilder', $result);
    }

    /** @test */
    public function it_set_the_TO_entity_data_in_the_property()
    {
        $elis = 1;

        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[setEntityAction]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('setEntityAction')
            ->once()
            ->with([0 => $elis], 'to')
            ->andReturn(['to' => 'jhon']);

        $result = $mockBuilder->to($elis);

        $this->assertInstanceOf('Fenos\Notifynder\Builder\NotifynderBuilder', $result);
    }

    /** @test */
    public function it_set_the_to_URL_data_in_the_property()
    {
        $url = 1;

        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[isString]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('isString')
            ->once()
            ->with($url)
            ->andReturn(true);

        $result = $mockBuilder->url($url);

        $this->assertInstanceOf('Fenos\Notifynder\Builder\NotifynderBuilder', $result);
    }

    /** @test */
    public function it_set_the_to_CATEGORY_ID_data_in_the_property()
    {
        $category = 1;

        $result = $this->notifynderBuilder->category($category);

        $this->assertInstanceOf('Fenos\Notifynder\Builder\NotifynderBuilder', $result);
    }

    /** @test */
    public function it_set_the_to_CATEGORY_NAME_data_in_the_property()
    {
        $category = "test";

        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[isString]', [$this->mockCategory]);

        $categoryModel = m::mock('Fenos\Notifynder\Models\NotificationjCategory');

        $this->mockCategory->shouldReceive('findByName')
            ->once()
            ->with($category)
            ->andReturn($categoryModel);

        $categoryModel->shouldReceive('id')
             ->once()
             ->with()
             ->andReturn(1);

        $result = $mockBuilder->category($category);

        $this->assertInstanceOf('Fenos\Notifynder\Builder\NotifynderBuilder', $result);
    }

    /** @test */
    public function it_allow_to_build_the_array_in_a_closure_for_more_flexibility()
    {
        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[getArray]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('getArray')
             ->once()
             ->with()
             ->andReturn(['array' => 1]);

        $result = $mockBuilder->raw(function ($builder) {
           return ['array'];
        });

        $this->assertEquals(['array' => 1], $result);
    }

    /** @test */
    public function it_allow_to_build_the_array_in_a_closure_for_more_flexibility_returning_null()
    {
        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[getArray]', [$this->mockCategory]);

        $result = $mockBuilder->raw(function ($builder) {
            return;
        });

        $this->assertFalse($result);
    }

    /** @test */
    public function it_set_the_to_EXTRA_data_in_the_property()
    {
        $extra = 1;

        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[isString]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('isString')
            ->once()
            ->with($extra)
            ->andReturn(true);

        $result = $mockBuilder->extra($extra);

        $this->assertInstanceOf('Fenos\Notifynder\Builder\NotifynderBuilder', $result);
    }

    /** @test */
    public function it_loop_an_notifynder_build_for_make_multinotifications_easyly()
    {
        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[isIterable,getArray]', [$this->mockCategory]);

        $dataToIterate = [1, 1, 1];
        $edataIterated = [[1],[1],[1]];

        $mockBuilder->shouldReceive('isIterable')
             ->once()
             ->with($dataToIterate)
             ->andReturn(true);

        $mockBuilder->shouldReceive('getArray')
            ->times(3)
            ->with()
            ->andReturn([1]);

        $result = $mockBuilder->loop($dataToIterate, function () {
           return 1;
        });

        $this->assertEquals($edataIterated, $result);
        $this->assertTrue(is_array($result));
    }

    /** @test */
    public function it_set_the_to_EntityFrom_data_in_the_property()
    {
        $from = [1];
        $property = "from";

        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[hasEntity,isNumeric]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('hasEntity')
            ->once()
            ->with($from)
            ->andReturn(false);

        $mockBuilder->shouldReceive('isNumeric')
            ->once()
            ->with($from[0])
            ->andReturn(false);

        $result = $mockBuilder->setEntityAction($from, $property);

        $this->assertEquals(['from_id' => 1], $result);
    }

    /** @test */
    public function it_set_the_to_EntityFromPolymorphic_data_in_the_property()
    {
        $fromType = "User";
        $from = [$fromType,1];
        $property = "from";

        $mockBuilder = m::mock('Fenos\Notifynder\Builder\NotifynderBuilder[hasEntity,isNumeric,isString]', [$this->mockCategory]);

        $mockBuilder->shouldReceive('hasEntity')
            ->once()
            ->with($from)
            ->andReturn(true);

        $mockBuilder->shouldReceive('isString')
            ->once()
            ->with($fromType)
            ->andReturn(true);

        $mockBuilder->shouldReceive('isNumeric')
            ->once()
            ->with($from[1])
            ->andReturn(false);

        $result = $mockBuilder->setEntityAction($from, $property);

        $this->assertEquals(['from_type' => $fromType, 'from_id' => 1], $result);
    }

    /** @test */
    public function it_check_if_as_entity_counting_the_parameters()
    {
        $result = $this->notifynderBuilder->hasEntity([1, 2]);

        $this->assertTrue($result);
    }
}
