<?php

class SenderManagerTest extends NotifynderTestCase
{
    public function testCallWithoutArguments()
    {
        $this->expectException(BadMethodCallException::class);

        $manager = app('notifynder.sender');
        $manager->sendSingle();
    }

    public function testCallUndefinedMethod()
    {
        $this->expectException(BadMethodCallException::class);

        $manager = app('notifynder.sender');
        $manager->undefinedMethod([]);
    }

    public function testCallFailingSender()
    {
        $this->expectException(BadFunctionCallException::class);

        $manager = app('notifynder.sender');
        $manager->extend('sendFail', function () {
        });
        $manager->sendFail([]);
    }
}
