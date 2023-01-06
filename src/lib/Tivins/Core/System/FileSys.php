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

    public static function copy(string $source, string $destination): bool
    {
        self::mkdirFile($destination);
        return copy($source, $destination);
    }
    /**
     * Create the directory for the given file.
     */
    public static function mkdirFile(string $filename, int $permissions = 0755): bool
    {
        return self::mkdir(dirname($filename), $permissions);
    }

    public static function getIterator(string $directory): RecursiveIteratorIterator
    {
        $directory = new RecursiveDirectoryIterator($directory);
        return new RecursiveIteratorIterator($directory);
    }
}