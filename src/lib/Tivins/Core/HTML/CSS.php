<?php

namespace Tivins\Core\HTML;

use Tivins\Core\Http\ContentType;
use Tivins\Core\Http\HTTP;

class CSS
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
        if (!file_exists($f)) {
            return '/*file_not_readable*/';
        }
        $nl = "";
        $css = join($nl, array_map('trim', file($f)));
        $css = preg_replace('~/\*(.|\n)*?\*/~', '', $css);
        $css = preg_replace('~\n{2,}~', $nl, $css);
        return trim($css);
    }
}