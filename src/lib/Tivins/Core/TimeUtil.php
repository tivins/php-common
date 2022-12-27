<?php

namespace Tivins\Core;

class TimeUtil
{
    /**
     * Gets the current timestamp in milliseconds.
     */
    public static function getMilliseconds(): int
    {
        return floor(microtime(true) * 1000);
    }

    public static function formatTime(int $timestamp, string $format = 'Y-m-d H:i:s T'): string
    {
        return gmdate($format, $timestamp);
    }
}