<?php

namespace Tivins\Core\System;

use Tivins\Core\Log\Level;

class Terminal
{
    public static function decorateSuccess(string $str): string { return self::decorate(Level::SUCCESS, $str); }
    public static function decorateDanger(string $str): string { return self::decorate(Level::DANGER, $str); }
    public static function decorateInfo(string $str): string { return self::decorate(Level::INFO, $str); }

    public static function decorate(Level $level, string $str): string
    {
        return match ($level) {
            Level::DANGER   => "\033[31m$str\033[0m",
            Level::SUCCESS  => "\033[32m$str\033[0m",
            Level::WARNING  => "\033[33m$str\033[0m",
            Level::INFO     => "\033[36m$str\033[0m",
            Level::DEBUG    => "\033[37m$str\033[0m",
            default         => $str,
        };
    }
}