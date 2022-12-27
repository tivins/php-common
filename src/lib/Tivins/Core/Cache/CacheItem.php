<?php

namespace Tivins\Core\Cache;

/**
 * Represent an element of the cache system. The content itself and the associated meta-data.
 *
 * Example. When you grab a remote content using cURL, you probably want to store the body
 * of the response, but also some information, such as time, status, ...etc.
 *
 * @see \Tivins\Core\Cache\Cache
 */
readonly class CacheItem
{
    public function __construct(
        public string $data,
        public mixed  $meta = null,
    )
    {
    }
}