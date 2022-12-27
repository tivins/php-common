<?php

namespace Tivins\Core\System;

use Tivins\Core\Color;

class Terminal
{
    // also: chr(27) or "\033"
    public const ESCAPE_CHAR = "\e";

    public static function savePosition(): void
    {
        echo self::ESCAPE_CHAR . '[s';
    }

    public static function restorePosition(): void
    {
        echo self::ESCAPE_CHAR . '[u';
    }

    public static function clearLine(): void
    {
        echo "\r" . self::ESCAPE_CHAR . '[K';
    }

    public static function goUp(int $nbLines): void
    {
        echo self::ESCAPE_CHAR . '[' . $nbLines . 'A';
    }

    public static function goUpClean(int $nbLines): void
    {
        while ($nbLines--) {
            static::goUp(1);
            static::clearLine();
        }
    }

    public static function goDown(int $nbLines): void
    {
        echo self::ESCAPE_CHAR . '[' . $nbLines . 'B';
    }

    public static function decorateRGB(?Color $foreColor, ?Color $backColor): void
    {
        if ($foreColor) echo self::ESCAPE_CHAR . "[38;2;{$foreColor->red};{$foreColor->green};{$foreColor->blue}m";
        if ($backColor) echo self::ESCAPE_CHAR . "[48;2;{$backColor->red};{$backColor->green};{$backColor->blue}m";
    }

    public static function decorateReset(): void
    {
        echo self::ESCAPE_CHAR . '[0m';
    }

    public static function width(): int
    {
        return intval(shell_exec('tput cols'));
    }

    public static function height(): int
    {
        return intval(shell_exec('tput lines'));
    }
}