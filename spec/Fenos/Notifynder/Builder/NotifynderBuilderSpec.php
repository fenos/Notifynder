<?php

namespace spec\Fenos\Notifynder\Builder;

use Carbon\Carbon;
use Fenos\Notifynder\Builder\NotifynderBuilder;
use Fenos\Notifynder\Categories\CategoryManager;
use Fenos\Notifynder\Models\NotificationCategory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotifynderBuilderSpec extends ObjectBehavior
{
    public function let(CategoryManager $category)
    {
        $this->beConstructedWith($category);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Fenos\Notifynder\Builder\NotifynderBuilder');
    }

    /** @test */
    function it_set_the_FROM_value_with_a_single_entity()
    {
        $user_id = 1;

        $this->from($user_id)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_set_the_FROM_value_giving_a_polymorphic_entity()
    {
        $user_id = 1;
        $user_class = 'User';

        $this->from($user_class,$user_id)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_set_the_FROM_value_giving_a_polymorphic_entity_the_first_value_must_be_the_class_entity()
    {
        $user_id = 1;
        $user_class = 'User';

        $this->shouldThrow('InvalidArgumentException')->during('from',[$user_id,$user_class]);
    }

    /** @test */
    function it_set_the_TO_value_with_a_single_entity()
    {
        $user_id = 1;

        $this->to($user_id)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_set_the_TO_value_giving_a_polymorphic_entity()
    {
        $user_id = 1;
        $user_class = 'User';

        $this->to($user_class,$user_id)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_set_the_TO_value_giving_a_polymorphic_entity_the_first_value_must_be_the_class_entity()
    {
        $user_id = 1;
        $user_class = 'User';

        $this->shouldThrow('InvalidArgumentException')->during('to',[$user_id,$user_class]);
    }

    /** @test */
    function it_add_the_url_parameter_to_the_builder()
    {
        $url = 'www.notifynder.io';

        $this->url($url)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_allow_only_string_as_url()
    {
        $url = 1;

        $this->shouldThrow('InvalidArgumentException')->during('url',[$url]);
    }

    /** @test */
    function it_add_the_expire_parameter_to_the_builder()
    {
        $datetime = new Carbon;

        $this->expire($datetime)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_allow_only_carbon_instance_as_expire_time()
    {
        $datetime = 1;

        $this->shouldThrow('InvalidArgumentException')->during('expire',[$datetime]);
    }


    /** @test */
    function it_add_a_category_id_to_the_builder()
    {
        $category_id = 1;

        $this->category($category_id)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_add_a_category_id_to_the_builder_givin_the_name_of_it(CategoryManager $category, NotificationCategory $categoryModel)
    {
        $name = 'category.name';
        $category_id = 1;

        $category->findByName($name)->shouldBeCalled()
                 ->willReturn($categoryModel);

        $categoryModel->getAttribute('id')->willReturn($category_id);

        $this->category($name)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_add_the_extra_parameter_to_the_builder()
    {
        $extra = ['my'  => 'extra'];

        $this->extra($extra)->shouldReturnAnInstanceOf(NotifynderBuilder::class);
    }

    /** @test */
    function it_allow_only_associative_array_as_extra_parameter_they_llbe_converted_in_jon()
    {
        $extra = ['my'];

        $this->shouldThrow('InvalidArgumentException')->during('extra',[$extra]);
    }

    /** @test */
    function it_create_a_builder_array_using_a_raw_closure()
    {
        date_default_timezone_set('UTC');

        $closure = function(NotifynderBuilder $builder)
        {
            return $builder->to(1)->from(2)->url('notifynder.io')->category(1);
        };

        $this->raw($closure)->shouldHaveKey('to_id');
        $this->raw($closure)->shouldHaveKey('from_id');
        $this->raw($closure)->shouldHaveKey('url');
        $this->raw($closure)->shouldHaveKey('category_id');
        $this->raw($closure)->shouldHaveKey('created_at');
        $this->raw($closure)->shouldHaveKey('updated_at');
    }

    public function it_create_multi_notification_in_a_loop()
    {
        $cloure = function(NotifynderBuilder $builder,$data,$key)
        {
            return $builder->to(1)->from(2)->url('notifynder.io')->category(1);
        };
    }
}
