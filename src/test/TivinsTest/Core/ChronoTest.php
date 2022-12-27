<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Chrono;

class ChronoTest extends TestCase
{
    public function testChrono()
    {
        $chrono = new Chrono();
        $chrono->start();
        usleep(100);
        $this->assertGreaterThan(0, $chrono->get());
    }
    public function testChrono2()
    {
        $chrono = (new Chrono())->start();
        usleep(100);
        $this->assertGreaterThan(0, $chrono->getReset());
        $this->assertLessThan(100, $chrono->get());
    }
}