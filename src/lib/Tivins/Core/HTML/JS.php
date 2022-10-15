<?php

namespace Tivins\Core\HTML;

use Tivins\Core\Http\ContentType;
use Tivins\Core\Http\HTTP;

class JS
{
    private array $files = [];

    public function add(string $file): void
    {
        $this->files[] = $file;
    }

    public function render(): never
    {
        HTTP::send(join(
            array_map(
                fn($f) => $this->getFile($f),
                $this->files
            )
        ), ContentType::CSS);
    }

    public function getFile(string $f): string
    {
        $nl = "";
        $css = join("\n",
            array_filter(
                array_map('trim', file($f)),
                fn(string $l) => $l != '' && !str_starts_with($l, '//')
            )
        );
        $css = preg_replace('~/\*(.|\n)*?\*/~', '', $css); // Remove multi-line comments
        $stored_strings = [];
        $css = preg_replace_callback(
            '~\'(.*?)\'~',
            function (array $matches) use (&$stored_strings) {
                $stored_strings[sha1($matches[0])] = $matches[0];
                return 'JS_STR_' . sha1($matches[0]) . '_JSSE';
            },
            $css
        );
        $reps = [
            '\s*:\s*'  => ':',
            '\s*,\s*'  => ',',
            '\s*=\s*'  => '=',
            '\s*\+\s*' => '+',
            '\s*-\s*'  => '-',
            '\s*/\s*'  => '/',
            '\s{1,};'  => ';',
        ];
        foreach ($reps as $regexp => $rep) {
            $css = preg_replace('~' . $regexp . '~', $rep, $css);
        }
        $css = preg_replace_callback(
            '~JS_STR_(.*?)_JSSE~',
            function ($matches) use ($stored_strings) {
                return $stored_strings[$matches[1]];
            },
            $css
        );
        $css = preg_replace('~\n{2,}~', $nl, $css); // remove empty lines
        return trim($css);
    }
}