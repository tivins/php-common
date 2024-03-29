<?php

namespace TivinsTest\Core;

use Tivins\Core\Http\QueryString;
use Tivins\Core\StrUtil;

class StringUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testToLowerDashed()
    {
        $this->assertEquals('this-is-a-simple-string', StrUtil::toLowerDashed('This is a Simple String'));
        $this->assertEquals('this-is-a-simple-string', StrUtil::toLowerDashed('This is (a) Simple String'));
        $this->assertEquals('this-is-1-simple-string', StrUtil::toLowerDashed('This is 1 "Simple" String'));
        $this->assertEquals('this-is-a-simple-string', StrUtil::toLowerDashed('This *is* _a_, "Simple" String !'));
    }

    public function testHTML()
    {
        $this->assertEquals('&lt;script&gt;', StrUtil::html('<script>'));
    }

    public function testIsStrongPassword()
    {
        $tests = [
            // password => is string
            'admin' => false,
            'admin12' => false,
            'adm!n12' => false,
            'superadm!n12' => false,
            'superAdm!n12' => true,
        ];
        foreach ($tests as $password => $expected) {
            self::assertEquals($expected, StrUtil::isStrongPassword($password));
        }

    }

    public function testQSJoin()
    {
        $p1 = '/path/';
        $p2 = '/sub/path/';
        $pout = QueryString::join($p1,$p2);
        self::assertEquals('/path/sub/path', $pout);
    }
}