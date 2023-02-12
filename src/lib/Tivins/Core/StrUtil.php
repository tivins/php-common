<?php

namespace Tivins\Core;

use Parsedown;

/**
 * Utility class for strings.
 */
class StrUtil
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

    public static function isStrongPassword(string $str): bool
    {
        return mb_strlen($str) > 7                   # 8 chars or more
            && preg_match('~\d~', $str)       # contain number
            && preg_match('~[[:upper:]]~u', $str)  # contain upper case
            && preg_match('~[[:lower:]]~u', $str)  # contain lower case
            && preg_match('~[[:punct:]]~u', $str)  # contain punctuation
            ;
    }

    public static function toUnicode(string $sequence = '\uffff'): string
    {
        return json_decode('"' . $sequence . '"');
    }

    public static function markdown(string $text, bool $thin = false): string {
        static $parser;
        if (!isset($parser)) {
            $parser = new Parsedown();
        }
        // $parser->setSafeMode(true);
        $html = $parser->text($text);
        if ($thin
            && str_starts_with($html, '<p>')
            && str_ends_with($html, '</p>')
            && substr_count($html, '</p>') == 1
        )
        {
            $html = substr($html, 3,-4);
        }
        return $html;
    }

    public static function hideIPs(string $s): string
    {
        return preg_replace('~(\d+)\.(\d+)\.(\d+)\.(\d+)~', 'xx.xx.xx.xx', $s);
    }

    /**
     * @param string $string The string to escape
     * @param string $quote `'` or `"`
     * @return string
     */
    public static function escape(string $str, string $quote): string
    {
        if ($quote == '"') {
            return json_encode($str);
        }
        return $quote
            . str_replace('\'', '\\\'', $str)
            . $quote;
    }
}