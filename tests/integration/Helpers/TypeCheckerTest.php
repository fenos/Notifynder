<?php

use Carbon\Carbon;
use Fenos\Notifynder\Helpers\TypeChecker;

class TypeCheckerTest extends NotifynderTestCase
{
    public function testIsString()
    {
        $this->assertTrue(TypeChecker::isString('hello world'));
    }

    public function testIsStringFailStrict()
    {
        $this->expectException(InvalidArgumentException::class);
        TypeChecker::isString(15);
    }

    public function testIsStringFail()
    {
        $this->assertFalse(TypeChecker::isString(15, false));
    }

    public function testIsNumeric()
    {
        $this->assertTrue(TypeChecker::isNumeric(15));
    }

    public function testIsNumericFailStrict()
    {
        $this->expectException(InvalidArgumentException::class);
        TypeChecker::isNumeric('hello world');
    }

    public function testIsNumericFail()
    {
        $this->assertFalse(TypeChecker::isNumeric('hello world', false));
    }

    public function testIsDate()
    {
        $this->assertTrue(TypeChecker::isDate(Carbon::now()));
    }

    public function testIsDateFailStrict()
    {
        $this->expectException(InvalidArgumentException::class);
        TypeChecker::isDate('hello world');
    }

    public function testIsDateFail()
    {
        $this->assertFalse(TypeChecker::isDate('hello world', false));
    }

    public function testIsArray()
    {
        $this->assertTrue(TypeChecker::isArray([1, 2, 3]));
    }

    public function testIsArrayFailStrict()
    {
        $this->expectException(InvalidArgumentException::class);
        TypeChecker::isArray(collect([1, 2, 3]));
    }

    public function testIsArrayFail()
    {
        $this->assertFalse(TypeChecker::isArray(collect([1, 2, 3]), false));
    }

    public function testIsIterable()
    {
        $this->assertTrue(TypeChecker::isIterable(collect([1, 2, 3])));
    }

    public function testIsIterableFailStrict()
    {
        $this->expectException(InvalidArgumentException::class);
        TypeChecker::isIterable([]);
    }

    public function testIsIterableFail()
    {
        $this->assertFalse(TypeChecker::isIterable([], false));
    }

    public function testIsNotification()
    {
        $this->assertTrue(TypeChecker::isNotification(new \Fenos\Notifynder\Models\Notification()));
    }

    public function testIsNotificationFailStrict()
    {
        $this->expectException(InvalidArgumentException::class);
        TypeChecker::isNotification([]);
    }

    public function testIsNotificationFail()
    {
        $this->assertFalse(TypeChecker::isNotification([], false));
    }
}
