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
        foreach (range(0, 100) as &$id) $id = md5($id);
        $this->assertGreaterThan(0, $chrono->step());
    }
}