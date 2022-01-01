<?php

namespace Tivins\Core\System;

class FileSys
{
    public static function mkdir(string $dir, int $permissions = 0755): bool
    {
        if (! is_dir($dir)) {
            mkdir($dir, $permissions, true);
        }
        return is_dir($dir);
    }

    public static function mkdirFile(string $filename, int $permissions = 0755): bool
    {
        return self::mkdir(dirname($filename), $permissions);
    }
}