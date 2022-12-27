<?php /** @noinspection PhpMultipleClassesDeclarationsInOneFile */

namespace Core\Code;

use Tivins\Core\Code\FastToString;
use PHPUnit\Framework\TestCase;

class Test
{
    use FastToString;
}

class FastToStringTest extends TestCase
{
    public function test__toString()
    {
        self::assertMatchesRegularExpression('~Core\\\Code\\\Test#\d*~', new Test);
    }
}
