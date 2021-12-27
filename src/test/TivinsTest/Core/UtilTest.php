<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testHTML()
    {
        self::assertEquals('&lt;script&gt;', \Tivins\Core\Util::html('<script>'));
    }
}