<?php

namespace Tivins\Core;

/**
 * Utility class for strings.
 */
class StringUtil
{
    /**
     * Convert a string to a lowercase and "hyphened" string.
     *
     * @param string $str The string to convert.
     * @return string The transformed string.
     *
     * @example
     *      echo StringUtil::toLowerDashed("A simple string"); // a-simple-string
     */
    public static function toLowerDashed(string $str): string
    {
        return trim(preg_replace('~[\W_]+~', '-', trim(mb_strtolower($str))), '-');
    }
}