<?php

namespace Tivins\Core\Http;


class QueryString
{
    private static array $query = [];

    public static function parse(): void
    {
        [$uri] = explode('?', $_SERVER['REQUEST_URI'], 2);
        self::$query = array_map('trim', preg_split('~/~', $uri, -1, PREG_SPLIT_NO_EMPTY));
    }

    public static function shift(): string
    {
        if (empty(self::$query)) return '';
        return array_shift(self::$query);
    }

    public static function at(int $index): string
    {
        return self::$query[$index] ?? '';
    }

    public static function all(): string
    {
        return join('/', self::$query);
    }

    public static function join(string ...$paths): string
    {
        return '/' . join('/', array_map(fn($s) => trim($s, '/'), $paths));
    }
}