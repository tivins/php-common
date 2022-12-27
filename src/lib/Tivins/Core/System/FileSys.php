<?php

namespace Tivins\Core\System;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileSys
{
    public static function mkdir(string $dir, int $permissions = 0755): bool
    {
        if (!is_dir($dir)) {
            mkdir($dir, $permissions, true);
        }
        return is_dir($dir);
    }

    public static function mkdirFile(string $filename, int $permissions = 0755): bool
    {
        return self::mkdir(dirname($filename), $permissions);
    }

    public static function isReadable(string $file): bool
    {
        return is_readable($file);
    }


    public static function globRecursive($directory): RecursiveIteratorIterator
    {
        $directory = new RecursiveDirectoryIterator($directory);
        return new RecursiveIteratorIterator($directory);
    }

}