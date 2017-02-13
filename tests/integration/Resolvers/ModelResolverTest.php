<?php

use Fenos\Notifynder\Models\Notification;

class ModelResolverTest extends NotifynderTestCase
{
    public function testGetModelDefault()
    {
        $resolver = app('notifynder.resolver.model');
        $class = $resolver->getModel(Notification::class);
        $this->assertEquals(Notification::class, $class);
    }

    public function testGetModelCustom()
    {
        $resolver = app('notifynder.resolver.model');
        $resolver->setModel(Notification::class, 'This\Model\Is\Custom');
        $class = $resolver->getModel(Notification::class);
        $this->assertEquals('This\Model\Is\Custom', $class);
    }

    public function testGetTableDefault()
    {
        $resolver = app('notifynder.resolver.model');
        $table = $resolver->getTable(Notification::class);
        $this->assertEquals('notifications', $table);
    }

    public function testGetTableCustom()
    {
        $resolver = app('notifynder.resolver.model');
        $resolver->setTable(Notification::class, 'prefixed_notifications');
        $table = $resolver->getTable(Notification::class);
        $this->assertEquals('prefixed_notifications', $table);
    }

    public function testMakeModel()
    {
        $resolver = app('notifynder.resolver.model');
        $model = $resolver->make(Notification::class);
        $this->assertInstanceOf(Notification::class, $model);
    }

    public function testMakeModelFail()
    {
        $this->expectException(ReflectionException::class);

        $resolver = app('notifynder.resolver.model');
        $resolver->setModel(Notification::class, 'This\Model\Is\Custom');
        $resolver->make(Notification::class);
    }
}
