<?php

namespace Tivins\Core\System;

class File
{
    public static function isReadable(string $file): bool
    {
        return is_readable($file);
    }

    # public static function get(string $file): string|false { return self::load($file); }
    # public static function getJSON(string $file): mixed { return self::loadJSON($file); }

    public static function loadJSON(string $file): mixed
    {
        $data = self::load($file);
        if ($data === false) {
            return null;
        }
        return json_decode($data);
    }


    public static function load(string $file): string|false
    {
        if (!is_readable($file)) {
            return false;
        }
        return file_get_contents($file);
    }

    public static function save(string $filename, mixed $data, bool $append = false, bool $createDirs = true): bool
    {
        if ($createDirs) FileSys::mkdirFile($filename);
        return file_put_contents($filename, $data, $append ? FILE_APPEND : 0) !== false;
    }

    public static function saveJSON(string $file, mixed $data): bool {
        $json = json_encode($data);
        if ($json === false) {
            return false;
        }
        return self::save($file, $json);
    }

    public static function delete(string $file): bool
    {
        return !file_exists($file) || unlink($file);
    }

    /**
     * Returns the extension of the given filename. Ex: 'image.JPG' => 'jpg'
     */
    public static function getExtension(string $file): string
    {
        $lastDotPos = strrpos($file, '.');
        if ($lastDotPos === false) {
            return '';
        }
        return mb_strtolower(mb_substr($file, $lastDotPos + 1));
    }
}