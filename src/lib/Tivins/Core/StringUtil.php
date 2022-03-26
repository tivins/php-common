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

    public static function html(string $str): string
    {
        return htmlentities($str, ENT_QUOTES, 'utf-8');
    }

    public static function isEmail(string $str): bool
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public static function isStrongPassword(string $str)
    {
        return mb_strlen($str) > 7                   # 8 chars or more
            && preg_match('~\d~', $str)       # contain number
            && preg_match('~[[:upper:]]~u', $str)  # contain upper case
            && preg_match('~[[:lower:]]~u', $str)  # contain lower case
            && preg_match('~[[:punct:]]~u', $str)  # contain punct
            ;
    }
}