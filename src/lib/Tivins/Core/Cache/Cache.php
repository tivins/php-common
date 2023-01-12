<?php

namespace Tivins\Core\Cache;

/**
 * Interface to implement to use a cache system.
 *
 * Usage:
 *
 * ```php
 * $cache = new MyCacheSystem();
 * $item = $cache->get('my_key', 3600);
 * if ($item) {
 *      // from cache !
 * }
 * else {
 *      // get what to be cached...
 *      $item = new CacheItem($content, $meta);
 *      $cache->set('my_key', $item);
 * }
 * ```
 *
 * @see \Tivins\Core\Cache\CacheItem
 */
interface Cache
{
    public const None      = 0;
    public const Unlimited = -1;

    public function set(string $key, CacheItem $item): bool;

    public function get(string $key, int $lifeTime = self::Unlimited): ?CacheItem;

    public function delete(string $key): void;

    public function exists(string $key): bool;

    public function getAge(string $key): int;

}

