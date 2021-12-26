<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;
use Tivins\Core\{OptionArg, OptionsArgs};

class OptionsArgsTest extends TestCase
{

    public function testParse()
    {
        $opts = (new OptionsArgs())
            ->add(new OptionArg('v', false, 'version'))
            ->parse();

        $this->assertEquals([], $opts);
    }
}
