<?php

class SenderManagerTest extends NotifynderTestCase
{
    public function testCallWithoutArguments()
    {
        $this->setExpectedException(BadMethodCallException::class);

        $manager = app('notifynder.sender');
        $manager->sendSingle();
    }

    public function testCallUndefinedMethod()
    {
        $this->setExpectedException(BadMethodCallException::class);

        $manager = app('notifynder.sender');
        $manager->undefinedMethod([]);
    }

    public function testCallFailingSender()
    {
        $this->setExpectedException(BadFunctionCallException::class);

        $manager = app('notifynder.sender');
        $manager->extend('sendFail', function () {
        });
        $manager->sendFail([]);
    }
}
