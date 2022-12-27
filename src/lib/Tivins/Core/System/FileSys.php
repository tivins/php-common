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

    public static function writeFile(string $filename, mixed $data, bool $append = false, bool $createDirs = true): bool
    {
        if ($createDirs) self::mkdirFile($filename);
        return file_put_contents($filename, $data, $append ? FILE_APPEND : 0) !== false;
    }

    public static function loadFile(string $file): string|false
    {
        if (!is_readable($file)) {
            return false;
        }
        return file_get_contents($file);
    }

    public static function isReadable(string $file): bool
    {
        return is_readable($file);
    }

    public static function delete(string $file): bool
    {
        return !file_exists($file) || unlink($file);
    }

    public static function globRecursive($directory): RecursiveIteratorIterator
    {
        $directory = new RecursiveDirectoryIterator($directory);
        return new RecursiveIteratorIterator($directory);
    }

    /**
     * Returns the extension of the given filename. Ex: 'image.JPG' => 'jpg'
     */
    public static function getFileExtension(string $file): string
    {
        $lastDotPos = strrpos($file, '.');
        if ($lastDotPos === false) {
            return '';
        }
        return mb_strtolower(mb_substr($file, $lastDotPos + 1));
    }
}