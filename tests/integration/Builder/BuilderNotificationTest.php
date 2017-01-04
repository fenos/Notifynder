<?php

use Fenos\Notifynder\Builder\Notification;

class BuilderNotificationTest extends NotifynderTestCase
{
    public function testPublicMethods()
    {
        $notification = new Notification();
        $notification->set('foo', 'bar');

        $this->assertInternalType('array', $notification->attributes());
        $this->assertCount(1, $notification->attributes());
        $this->assertArrayHasKey('foo', $notification->attributes());
        $this->assertTrue($notification->has('foo'));
        $this->assertSame('bar', $notification->attribute('foo'));
        $this->assertFalse($notification->isValid());

        $notification->set('category_id', 1);
        $notification->set('from_id', 1);
        $notification->set('to_id', 2);

        $this->assertTrue($notification->isValid());
    }

    public function testTypeChanger()
    {
        $notification = new Notification();
        $notification->set('category_id', 1);
        $notification->set('from_id', 1);
        $notification->set('to_id', 2);
        $notification->set('extra', ['foo' => 'bar']);

        $this->assertTrue($notification->isValid());
        $this->assertInternalType('array', $notification->toArray());
        $this->assertInternalType('array', $notification->toArray()['extra']);
        $this->assertInternalType('array', $notification->toDbArray());
        $this->assertInternalType('string', $notification->toDbArray()['extra']);
        $this->assertJson($notification->toJson());
        $this->assertInternalType('string', $notification->toString());
        $this->assertInternalType('string', (string) $notification);
    }

    public function testOverloaded()
    {
        $notification = new Notification();
        $notification->category_id = 1;
        $notification->from_id = 1;
        $notification->to_id = 2;

        $this->assertTrue($notification->isValid());
        $this->assertSame(1, $notification->category_id);
        $this->assertSame(1, $notification->from_id);
        $this->assertSame(2, $notification->to_id);
    }

    public function testOffsetMethods()
    {
        $notification = new Notification();
        $notification->offsetSet('foo', 'bar');
        $this->assertTrue($notification->offsetExists('foo'));
        $this->assertSame('bar', $notification->offsetGet('foo'));
        $notification->offsetUnset('foo');
        $this->assertFalse($notification->offsetExists('foo'));
    }

    public function testGetText()
    {
        $category = $this->createCategory();
        $from = $this->createUser();
        $to = $this->createUser();
        $notification = new Notification();
        $notification->set('category_id', $category->getKey());
        $notification->set('from_id', $from->getKey());
        $notification->set('to_id', $to->getKey());

        $this->assertSame('Notification send from #1 to #2.', $notification->getText());
    }
}
