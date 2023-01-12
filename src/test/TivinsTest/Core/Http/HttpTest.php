<?php

namespace TivinsTest\Core\Http;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Net\Http\Method as HttpMethod;
use Tivins\Core\Net\Http\Status as HttpStatus;

class HttpTest extends TestCase
{
    public function testEnum()
    {
        $this->assertEquals(404, HttpStatus::NotFound->value);
        $this->assertEquals('POST', HttpMethod::POST->name);
        $this->assertTrue(HttpStatus::NotFound->isError());
        $this->assertFalse(HttpStatus::OK->isError());
    }
}