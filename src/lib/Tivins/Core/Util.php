<?php

namespace Tivins\Core;

class Util
{
    static public function inline_str($str): string
    {
        $out = '';
        $idx = 0;
        while ($idx < mb_strlen($str)) {
            $chr = mb_substr($str, $idx, 1);
            if ($chr == " ") $chr = '·';
            if ($chr == "\n") $chr = '\n';
            if ($chr == "\r") $chr = '\r';
            if ($chr == "\t") $chr = '\t';
            $out .= $chr;
            $idx++;
        }
        return $out;
    }

    static public function yesno(bool $bool): string
    {
        return $bool ? 'yes' : 'no';
    }

    static public function get_last_key(&$array): int|string|null
    {
        end($array);
        return key($array);
    }

    static public function html(string $str): string
    {
        return htmlentities($str, ENT_QUOTES, 'utf-8');
    }

    static public function chrono_start(): float
    {
        return microtime(true);
    }

    static public function chrono_duration(float $start): float
    {
        return microtime(true) - $start;
    }
}