<?php

namespace Tivins\Core\System;

class FileSys
{
    public static function mkdirFile(string $filename, int $permissions = 0755): bool
    {
        $dir = dirname($filename);
        if (! is_dir($dir)) {
            mkdir($dir, $permissions, true);
        }
        return is_dir($dir);
    }
}