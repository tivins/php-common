<?php

namespace Tivins\Core\Cache;

use Tivins\Core\System\File;
use Tivins\Core\System\FileSys;

class FileCache implements Cache
{
    private string $directory;
    private int    $firstLevelSize = 2;

    public function __construct(string $directory = '/tmp')
    {
        $this->directory = $directory == '/' ? $directory : rtrim($directory, '/');
    }

    public function exists(string $key): bool
    {
        $filename = $this->getFilename($key);
        return file_exists($filename) && is_readable($filename);
    }

    public function getAge(string $key): int
    {
        if (!$this->exists($key)) {
            return PHP_INT_MAX;
        }
        return time() - filemtime($this->getFilename($key));
    }

    public function getFilename(string $key, bool $meta = false): string
    {
        $hash = sha1($key);
        return $this->directory
            . '/' . substr($hash, 0, $this->firstLevelSize)
            . '/' . $hash
            . ($meta ? '.meta' : '')
            ;
    }

    public function set(string $key, CacheItem $item): bool
    {
        $filename = $this->getFilename($key);
        FileSys::mkdirFile($filename);
        $res = File::save($filename, $item->data) !== false;
        if ($res && !is_null($item->meta)) {
            $res = File::saveJSON($this->getFilename($key, true), $item->meta);
        }
        return $res;
    }

    public function get(string $key, int $lifeTime = self::Unlimited): ?CacheItem
    {
        if ($lifeTime == self::None || !$this->exists($key)) return null;

        $age = $this->getAge($key);

        if ($lifeTime != -1 && $age > $lifeTime) {
            return null;
        }
        // echo "FROM CACHE $key\n";
        $metaName = $this->getFilename($key, true);
        return new CacheItem(
            file_get_contents($this->getFilename($key)),
            is_readable($metaName) ? json_decode(file_get_contents($metaName)) : null
        );
    }

    public function delete(string $key): void
    {
        File::delete($this->getFilename($key));
    }
}