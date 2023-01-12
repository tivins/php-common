<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Color;
use Tivins\Core\ColorFormat;
use function PHPUnit\Framework\assertEquals;

class ColorTest extends TestCase
{

    public function test__construct()
    {
        $color = new Color(255, 32, 20);
        assertEquals('ff2014', $color->format());
        assertEquals('255;32;20', $color->format(ColorFormat::TTY));
        assertEquals('rgb(255,32,20)', $color->format(ColorFormat::RGB));

        $color = (new Color(255, -32, 20*1000))->clamp();
        assertEquals('255;0;255', $color->format(ColorFormat::TTY));
    }
}
