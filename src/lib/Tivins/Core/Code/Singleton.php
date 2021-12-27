<?php

namespace Tivins\Core\Code;

use TivinsTest\Core\Code\SingletonTest;

/**
 * @see SingletonTest
 */
class Singleton
{
    /**
     * @var array<array-key, static>
     */
    private static array $instances = [];

    public static function &getInstance(): static
    {
        $class = static::class;
        if (empty(self::$instances[$class])) {
            self::$instances[$class] = new $class;
        }
        return self::$instances[$class];
    }

    public static function getInstances(): array
    {
        return self::$instances;
    }

    protected function __construct() { }
    /*final*/ private function __clone() { }
}
