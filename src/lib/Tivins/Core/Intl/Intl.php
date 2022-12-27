<?php

namespace Tivins\Core\Intl;

class Intl
{
    private static array $data = [];

    public static function setData($data): void
    {
        self::$data = $data;
    }

    public static function get(string $key, string|null $default = null): string
    {
        return self::$data[$key] ?? $default ?? $key;
    }

    public static function format(string $key, array $repl): string
    {
        return str_replace(array_keys($repl), array_values($repl), self::$data[$key] ?? $key);
    }

    // public static function mapTranslation(array &$arr, string $lang): void
    // {
    //     $class = get_class(reset($arr));
    //     [$class, 'mapTranslation']($arr, $lang);
    // }
}