<?php

namespace TivinsTest\Core\Code;

use Error;
use PHPUnit\Framework\TestCase;
use Tivins\Core\Code\Singleton;

class Class1 extends Singleton
{
}

class Class2 extends Singleton
{
    public function foo(): void
    {
    }
}

class SingletonTest extends TestCase
{
    public function testSingleton()
    {
        $class1a = Class1::getInstance();
        $class1b = Class1::getInstance();
        $this->assertTrue($class1a === $class1b);

        $class2a = Class2::getInstance();
        $class2a->foo();
        $this->assertCount(2, Singleton::getInstances());
    }

    /*
     * not really testable?
     *
    public function testClone()
    {
        $class1a = Class1::getInstance();
        $this->expectException(Error::class);
        clone($class1a);
    }
    */

}
