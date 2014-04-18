<?php

use Mockery as m;
use Fenos\Notifynder\Parse\NotifynderParse;

/**
* Test NofitynderParseTest Class
*/
class NofitynderParseTest extends PHPUnit_Framework_TestCase
{
	/**
	* @var
	*/
	protected $notifynderTranslator;

    /**
    * @var
    */
    protected $notifynder_model;

    /**
    * @var
    */
    protected $notifynderParse;

    public function setUp()
    {
        $collectionMock = m::mock('Illuminate\Database\Eloquent\Collection');

        $this->notifynderTranslator = m::mock('Fenos\Notifynder\Models\Collections\NotifynderTranslationCollection');

        $this->notifynderParse = new NotifynderParse(

            $this->notifynder_model =  $this->items()
        
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

    public function test_parse_notification()
    {
        $notifynderParse = m::mock('Fenos\Notifynder\Parse\NotifynderParse[getValues,replaceSpecialValues]',[$this->items()]);

        $special_values_parsed = [

            0 => 'extra',
            1 => 'user.name'

        ];

        $notifynderParse->shouldReceive('getValues')
                        ->once()
                        ->andReturn($special_values_parsed);

        $notifynderParse->shouldReceive('replaceSpecialValues')
                        ->with($special_values_parsed,0)
                        ->once()
                        ->andReturn($this->itemsParsed());

        // i mocked the method replaceSpecialValues so it didn't set the property items with the new 
        // results updated so i specific that here using reflection I do this manually but the method that 
        // merge the results (replaceSpecialValues) has been unit tested correctly 
        $reflection = new \ReflectionClass($notifynderParse);
        $reflection_property = $reflection->getProperty('items');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($notifynderParse, $this->itemsParsed());

        $result = $notifynderParse->parse();
        $this->assertEquals($this->itemsParsed(),$result);
    }

    public function test_extract_special_values_from_a_string()
    {
        $result = $this->notifynderParse->getValues($this->items()[0]['body']['text']);

        $assert = [

            0 => 'extra',
            1 => 'user.name'
        ];

        $this->assertEquals($assert,$result);
    }

    public function test_replace_special_value_with_new_value()
    {
        $special_values_parsed = [

            0 => 'extra',
            1 => 'user.name'

        ];

        $result = $this->notifynderParse->replaceSpecialValues($special_values_parsed,0);

        $this->assertEquals($this->itemsParsed(),$result);
    }

    public function items()
    {
       return ["0" => array(
                "id" => 150,
                "from_id" => 1,
                "to_id" => 2,
                "category_id" => 4,
                "url" => "www.foo.com",
                "extra" => "is cool",
                "read" => 0,
                "created_at" => "2014-04-16 23:23:49",
                "updated_at" => "2014-04-16 23:23:49",
                "body" => array(

                    "id" => 4,
                    "name" => "notifynder",
                    "text" => "notifynder is {extra} build by {user.name}",
                    "created_at" => "2014-04-16 23:23:36",
                    "updated_at" => "2014-04-16 23:23:36",
                ),

                "user" => array(
                
                    "id" => 1,
                    "email" => "admin@admin.com",
                    "name" => "fabrizio"
                )

            )
        ];
    }

    public function itemsParsed()
    {
        return ["0" => array(
                "id" => 150,
                "from_id" => 1,
                "to_id" => 2,
                "category_id" => 4,
                "url" => "www.foo.com",
                "extra" => "is cool",
                "read" => 0,
                "created_at" => "2014-04-16 23:23:49",
                "updated_at" => "2014-04-16 23:23:49",
                "body" => array(

                    "id" => 4,
                    "name" => "notifynder",
                    "text" => "notifynder is is cool build by fabrizio",
                    "created_at" => "2014-04-16 23:23:36",
                    "updated_at" => "2014-04-16 23:23:36",
                ),

                "user" => array(
                
                    "id" => 1,
                    "email" => "admin@admin.com",
                    "name" => "fabrizio"
                )

            )
        ];
    }

     /**
    * Determine if two associative arrays are similar
    *
    * Both arrays must have the same indexes with identical values
    * without respect to key ordering 
    * 
    * @param array $a
    * @param array $b
    * @return bool
    */
    function arrays_are_similar($a, $b) {
    // if the indexes don't match, return immediately
    if (count(array_diff_assoc($a, $b))) {
    return false;
    }
    // we know that the indexes, but maybe not values, match.
    // compare the values between the two arrays
    foreach($a as $k => $v) {
    if ($v !== $b[$k]) {
      return false;
    }
    }
    // we have identical indexes, and no unequal values
    return true;
}
}