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
    protected $notifynder_model;

    /**
    * @var
    */
    protected $notifynderParse;

    /**
     * @var
     */
    protected $extra;

    public function setUp()
    {
        $eloquent = m::mock('Illuminate\Database\Eloquent\Model');

        $this->notifynderParse = new NotifynderParse(

            $this->notifynder_model =  m::mock('Fenos\Notifynder\Models\Notification'),
            $this->extra = "is cool"
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
        $notifynderParse = m::mock('Fenos\Notifynder\Parse\NotifynderParse[getValues,replaceSpecialValues]',[$this->notifynder_model,$this->extra]);

        $body = $this->item()['body']['text'];
        $valuesToParse = [0,['extra' => $this->extra]];
        $categoryModel = m::mock('Illuminate\Database\Eloquent\Model');

        $this->notifynder_model->shouldReceive('getAttribute')
             ->once()
             ->with('body')
             ->andReturn($categoryModel);

        $categoryModel->shouldReceive('getAttribute')
            ->once()
            ->with('text')
            ->andReturn($body);

        $notifynderParse->shouldReceive('getValues')
             ->once()
             ->with($body)
             ->andReturn($valuesToParse);

        $notifynderParse->shouldReceive('replaceSpecialValues')
             ->once()
             ->with($valuesToParse,$this->notifynder_model,$body)
             ->andReturn($this->itemParsed()['body']['text']);

        $result = $notifynderParse->parse();

        $this->assertEquals($this->itemParsed()['body']['text'],$result);
    }

    public function test_replace_special_values()
    {
        $notifynderParse = m::mock('Fenos\Notifynder\Parse\NotifynderParse[insertValuesRelation,replaceExtraParameter]',[$this->notifynder_model,$this->extra]);
        $valuesToParse = ['is cool'];
        $body = $this->item()['body']['text'];

        $notifynderParse->shouldReceive('replaceExtraParameter')
             ->once()
             ->with('is cool',$body)
             ->andReturn($this->itemParsed()['body']['text']);

        $result = $notifynderParse->replaceSpecialValues($valuesToParse,$this->notifynder_model,$body);

        $this->assertEquals($this->itemParsed()['body']['text'],$result);
    }

    protected function item()
    {
        return [
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
        ];
    }

    public function itemParsed()
    {
        return [
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
        ];
    }
}
