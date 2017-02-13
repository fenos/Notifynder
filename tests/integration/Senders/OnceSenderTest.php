<?php

class OnceSenderTest extends NotifynderTestCase
{
    public function testGetQueryInstanceFail()
    {
        app('notifynder.resolver.model')->setModel(\Fenos\Notifynder\Models\Notification::class, \Fenos\Tests\Models\FakeModel::class);

        $this->expectException(BadMethodCallException::class);

        $manager = app('notifynder.sender');
        $manager->sendOnce([
            new \Fenos\Notifynder\Builder\Notification(),
        ]);
    }
}
