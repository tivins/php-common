<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;
use Tivins\Core\{OptionArg, OptionsArgs};

class OptionsArgsTest extends TestCase
{

    public function testParse()
    {
        $opts = OptionsArgs::newParsed(
            new OptionArg('uri', true, 'u'),
             new OptionArg('configuration', true),
             new OptionArg('notify-id', true),
             new OptionArg('verbose', false, 'v'),
        );

        self::assertFalse($opts->getValue('notify-id'));
//        self::assertIsString($opts->getValue('configuration'));
    }
}
