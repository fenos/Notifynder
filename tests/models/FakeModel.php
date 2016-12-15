<?php

namespace Fenos\Tests\Models;

class FakeModel
{
    public static function query()
    {
        return new static();
    }
}
