<?php

namespace TivinsTest\Core\Http;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Http\Status as HttpStatus;

class StatusTest extends TestCase
{
    public function testEnum()
    {
        $this->assertEquals(404, HttpStatus::NotFound->value);
        $this->assertTrue(HttpStatus::NotFound->isError());
    }
}