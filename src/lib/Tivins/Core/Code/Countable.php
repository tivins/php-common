<?php

namespace Tivins\Core\Code;

class Countable
{
    private static array $counter = [];
    public readonly int $id;

    public function __construct()
    {
        if (!isset(self::$counter[static::class])) {
            self::$counter[static::class]=0;
        }
        self::$counter[static::class]++;
        $this->id = self::$counter[static::class];
    }
}