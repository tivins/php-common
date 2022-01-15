<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Tpl;

class TplTest extends TestCase
{
    public function testTpl()
    {
        $tpl = new Tpl('test');
        $this->assertEquals('test', (string)$tpl);

        $tpl = Tpl::fromFile(__dir__ . '/TplTest.tpl1.html');
        $this->assertEmpty(trim($tpl));

        $tpl->block('blockName', ['variable' => 'hello1']);
        $tpl->block('blockName', ['variable' => 'hello2']);
        $this->assertEquals(
            '<p>A block that contains a \'hello1\'.</p>'
            . "\n\n"
            . '<p>A block that contains a \'hello2\'.</p>',
            trim($tpl));

        $tpl->block('blockName2', ['num' => '2.7']);
        $this->assertEquals(
            '<p>A block that contains a \'hello1\'.</p>'
            . "\n\n"
            . '<p>A block that contains a \'hello2\'.</p>'
            . "\n"
            . "\n\n"
            . '<div>3</div>'
            ,
            trim($tpl));

    }
}