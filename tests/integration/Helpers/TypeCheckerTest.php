<?php

use Carbon\Carbon;
use Fenos\Notifynder\Helpers\TypeChecker;

class TypeCheckerTest extends NotifynderTestCase
{
    protected $checker;

    public function setUp()
    {
        parent::setUp();
        $this->checker = new TypeChecker();
    }

    public function testIsString()
    {
        $this->assertTrue($this->checker->isString('hello world'));
    }

    public function testIsStringFail()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->assertTrue($this->checker->isString(15));
    }

    public function testIsNumeric()
    {
        $this->assertTrue($this->checker->isNumeric(15));
    }

    public function testIsNumericFail()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->assertTrue($this->checker->isNumeric('hello world'));
    }

    public function testIsDate()
    {
        $this->assertTrue($this->checker->isDate(Carbon::now()));
    }

    public function testIsDateFail()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->assertTrue($this->checker->isDate('hello world'));
    }

    public function testIsArray()
    {
        $this->assertTrue($this->checker->isArray([1, 2, 3]));
    }

    public function testIsArrayFail()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->assertTrue($this->checker->isArray([]));
    }

    public function testIsIterable()
    {
        $this->assertTrue($this->checker->isIterable(collect([1, 2, 3])));
    }

    public function testIsIterableFail()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $this->assertTrue($this->checker->isIterable([]));
    }
}
