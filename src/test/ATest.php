<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class A
{
    public static function func(self $obj): int
    {
        $helperData = $obj->helper();
        return $helperData + 1;
    }

    public function helper(): int
    {
        return 5;
    }
}

final class ATest extends TestCase
{
    public function testFunc(): void
    {
        $stub = $this->createStub(A::class);
        $stub->method('helper')->willReturn(2);
        $this->assertEquals(3, A::func($stub));
    }
}