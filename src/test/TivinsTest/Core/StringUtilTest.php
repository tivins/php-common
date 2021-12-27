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
}