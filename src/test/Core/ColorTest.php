<?php

namespace Core;

use Tivins\Core\Color;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class ColorTest extends TestCase
{

    public function test__construct()
    {
        $color = new Color(255, 32, 20);
        assertEquals('ff2014', $color->toHexString());
    }
}
