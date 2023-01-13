<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Tpl;
use Tivins\Core\Intl\Intl;

class TplTest extends TestCase
{
    public function testOK() {
        self::assertTrue(true);
    }
    
    public function testTpl()
    {
        $tpl = new Tpl('test');
        $this->assertEquals('test', (string)$tpl);

        $tpl = Tpl::fromFile(__dir__ . '/../../testFiles/TplTest.tpl1.html');
        $this->assertEmpty(trim($tpl));

        $tpl->block('blockName', ['variable' => 'hello1']);
        $tpl->block('blockName', ['variable' => 'hello2']);
        //$this->assertEquals(
        //    '<p>A block that contains a \'hello1\'.</p>'
        //    . "\n\n"
        //    . '<p>A block that contains a \'hello2\'.</p>',
        //    trim($tpl));
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

    public function _testTpl2()
    {
       //  $i18n = (new Intl())->setData(['hello' => 'Bonjour']);
        Intl::setData(['hello' => 'Bonjour']);
        $tpl = Tpl::fromFile(__dir__ . '/../../testFiles/replacements.html');
        //$tpl->setI18nModule($i18n);
        $tpl->setVars([
            'var1' => 'Template!',
            'html' => '<html>',
        ]);
        self::assertEquals("var1=Template!\nhtml=<html>\nhello=Bonjour", (string)$tpl);
    }
}
