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

        $this->notifynderParse = new NotifynderParse();
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
        $notifynderParse = m::mock('Fenos\Notifynder\Parse\NotifynderParse[getValues,replaceSpecialValues]');

        $body = $this->item()['body']['text'];
        $valuesToParse = [0,['extra' => $this->extra]];
        $item = m::mock('Fenos\Notifynder\Models\Notification');
        $model = m::mock('Illuminate\Database\Eloquent\Model');

        $item->shouldReceive('getAttribute')
             ->once()
             ->with('body')
             ->andReturn($model);

        $model->shouldReceive('getAttribute')
             ->once()
             ->with('text')
             ->andReturn($body);

        $notifynderParse->shouldReceive('getValues')
             ->once()
             ->with($body)
             ->andReturn($valuesToParse);

        $notifynderParse->shouldReceive('replaceSpecialValues')
             ->once()
             ->with($valuesToParse, $item, $body, $this->item()['extra'])
             ->andReturn($this->itemParsed()['body']['text']);

        $result = $notifynderParse->parse($item, $this->item()['extra']);

        $this->assertEquals($this->itemParsed()['body']['text'], $result);
    }

    public function test_replace_extra_special_values()
    {
        $notifynderParse = m::mock('Fenos\Notifynder\Parse\NotifynderParse[insertValuesRelation,replaceExtraParameter]');
        $valuesToParse = ['is cool'];
        $body = $this->item()['body']['text'];
        $item = m::mock('Fenos\Notifynder\Models\Notification');

        $notifynderParse->shouldReceive('replaceExtraParameter')
             ->once()
             ->with('is cool', $body, $this->item()['extra'])
             ->andReturn($this->itemParsed()['body']['text']);

        $result = $notifynderParse->replaceSpecialValues($valuesToParse, $item, $body, $this->item()['extra']);

        $this->assertEquals($this->itemParsed()['body']['text'], $result);
    }

    public function test_replace_values_relation_special_values()
    {
        $notifynderParse = m::mock('Fenos\Notifynder\Parse\NotifynderParse[insertValuesRelation,replaceExtraParameter]');
        $valuesToParse = ['from.hello'];
        $body = $this->item()['body']['text'];
        $item = m::mock('Fenos\Notifynder\Models\Notification');

        $notifynderParse->shouldReceive('insertValuesRelation')
            ->once()
            ->with(['hello'], 'from', $body, $item)
            ->andReturn($this->itemParsed()['body']['text']);

        $result = $notifynderParse->replaceSpecialValues($valuesToParse, $item, $body, null);

        $this->assertEquals($this->itemParsed()['body']['text'], $result);
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
                "name" => "fabrizio",
            ),
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
                "name" => "fabrizio",
            ),
        ];
    }
}
