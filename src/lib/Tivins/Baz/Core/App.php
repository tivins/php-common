<?php

namespace Tivins\Baz\Core;

class App
{
    private static int $timeStart = 0;

    public static function start(): void
    {
        self::$timeStart = microtime(true);
    }

    public static function getTimeStart(): int
    {
        return self::$timeStart;
    }
}