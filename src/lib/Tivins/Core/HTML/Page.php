<?php

namespace Tivins\Core\HTML;

use Tivins\Core\Http\HTTP;

class Page {
    public static string $title = '';
    public static array $css = [];
    public static array $scripts = [];
    public static function getScripts(): string {
        return join(array_map(fn($s) => '<script src="'.$s.'"></script>', self::$scripts));
    }
    public static function getCSS(): string {
        return join(array_map(fn($s) => '<link rel="stylesheet" type="text/css" href="'.$s.'">', self::$css));
    }

    public static function render(): never {
        $content = '<!doctype html>
<html lang="en">
  <head>
    <title>'.self::$title.'</title>
    <meta charset="utf-8">
    '.self::getCSS().'
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="manifest" href="/site.webmanifest">
  </head>
  <body>
    <div id="body"></div>
    '.self::getScripts().'
  </body></html>';
        HTTP::send($content);
    }
}