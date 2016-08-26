<?php

class OnceSenderTest extends NotifynderTestCase
{
    public function testGetQueryInstanceFail()
    {
        notifynder_config()->set('notification_model', \Fenos\Tests\Models\FakeModel::class);

        $this->setExpectedException(BadMethodCallException::class);

        $manager = app('notifynder.sender');
        $manager->sendOnce([
            new \Fenos\Notifynder\Builder\Notification(),
        ]);
    }
}
