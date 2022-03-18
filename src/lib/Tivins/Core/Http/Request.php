<?php

namespace Tivins\Core\Http;

class Request
{
    public static function isCLI(): bool
    {
        return PHP_SAPI == 'cli';
    }
}