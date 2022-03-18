<?php

namespace Tivins\Core\Http;


class QueryString
{
    private static array $query = [];

    public static function parse(): void
    {
        self::$query = array_map('trim', preg_split('~/~', $_SERVER['REQUEST_URI'], -1, PREG_SPLIT_NO_EMPTY));
    }

    public static function at(int $index): string
    {
        return self::$query[$index] ?? '';
    }
}