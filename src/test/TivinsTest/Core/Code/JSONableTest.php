<?php

namespace TivinsTest\Core\Code;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Code\JSONable;

class A extends JSONable {
    protected int $field = 213;
    protected string $field2 = 'abc';
}

class JSONableTest extends TestCase
{
    public function testJSON()
    {
        $result = json_encode(new A);
        self::assertEquals(json_encode(['field'=>213,'field2'=>'abc']), $result);
    }
}