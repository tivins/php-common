<?php

/**
 * @noinspection PhpMultipleClassesDeclarationsInOneFile
 */

namespace TivinsTest\Core\Code;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Code\EnumExtra;

enum EnumTest: int
{
    use EnumExtra;

    case A = 1;
    case B = 2;
}

class EnumExtraTest extends TestCase
{
    public function testTryFromName() {
        self::assertEquals(EnumTest::A, EnumTest::tryFromName("A"));
    }
    public function testGetAssociative() {
        $data = EnumTest::getAssociative();
        self::assertEquals([
            1 => 'tivinstest_core_code_enumtest_A',
            2 => 'tivinstest_core_code_enumtest_B',
        ], $data);
    }
}