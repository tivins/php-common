<?php

namespace Tivins\Core\System;

use Exception;

class Assert
{
    /**
     * @throws Exception
     */
    public static function equals($expected, $actual): void
    {
        if ($expected !== $actual) {
            throw new Exception(__method__ . ':' . var_export(func_get_args(), true));
        }
    }

    /**
     * @throws Exception
     */
    public static function notFalse($actual): void
    {

        if ($actual === false) {
            throw new Exception(__method__ . ':' . var_export(func_get_args(), true));
        }
    }

    /**
     * @throws Exception
     */
    public static function inArray($actual, array $haystack): void
    {
        if (!in_array($actual, $haystack)) {
            throw new Exception(__method__ . ':' . var_export(func_get_args(), true));
        }
    }

    /**
     * @throws Exception
     */
    public static function true(bool $actual): void
    {
        if ($actual !== true) {
            throw new Exception(__method__ . ':' . var_export(func_get_args(), true));
        }
    }
}