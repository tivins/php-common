<?php

namespace TivinsTest\Core;

use Tivins\Core\StringUtil;

class StringUtilTest extends \PHPUnit\Framework\TestCase
{
    public function testToLowerDashed()
    {
        $this->assertEquals('this-is-a-simple-string', StringUtil::toLowerDashed('This is a Simple String'));
        $this->assertEquals('this-is-a-simple-string', StringUtil::toLowerDashed('This is (a) Simple String'));
        $this->assertEquals('this-is-1-simple-string', StringUtil::toLowerDashed('This is 1 "Simple" String'));
        $this->assertEquals('this-is-a-simple-string', StringUtil::toLowerDashed('This *is* _a_, "Simple" String !'));
    }

    public function testHTML()
    {
        $this->assertEquals('&lt;script&gt;', StringUtil::html('<script>'));
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
            self::assertEquals($expected, StringUtil::isStrongPassword($password));
        }

    }
}