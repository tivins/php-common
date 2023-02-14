<?php

namespace Tivins\Core\Net;

use CurlHandle;
use Tivins\Core\Cache\Cache;
use Tivins\Core\Cache\CacheItem;
use Tivins\Core\Code\FastToString;

class Client extends ClientConfig
{
    use FastToString;

    private CurlHandle|false $handle;
    private string $url;
    private array $info;
    private string $content = '';
    private Cache|null $cache = null;
    private int $cacheLifeTime = Cache::None;
    protected float $duration = 0;

    public function __construct(string $url, ?Cache $cache = null, int $cacheLifeTime = Cache::None)
    {
        parent::__construct();
        $this->url = $url;
        $this->setCache($cache, $cacheLifeTime);
    }

    public function __destruct()
    {
        if (isset($this->handle)) {
            curl_close($this->handle);
        }
    }

    /**
     * This method need to be exposed, to be used by ClientMulti.
     * @see ClientMulti::addClients()
     */
    public function prepare(): static
    {
        $this->handle = curl_init($this->url);
        curl_setopt_array($this->handle, $this->build());
        return $this;
    }

    public function setCache(?Cache $cache, int $cacheLifeTime = Cache::None): static
    {
        $this->cache = $cache;
        $this->cacheLifeTime = $cacheLifeTime;
        return $this;
    }

    public function getURL(): string
    {
        return $this->url;
    }

    public function getHandle(): false|CurlHandle
    {
        return $this->handle;
    }

    public function getCode(): int
    {
        return $this->getInfo()['http_code'];
    }

    public function getInfo(): array
    {
        if (!isset($this->info)) {
            $this->info = curl_getinfo($this->handle);
        }
        return $this->info;
    }

    public function setFromMultipleContent(): static
    {
        $this->content = curl_multi_getcontent($this->handle);
        $this->cache?->set($this->url, new CacheItem($this->content, $this->getInfo()));
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentAsJSON(): mixed
    {
        return json_decode($this->content);
    }

    public function fromCache(): bool
    {
        if (!$this->cache) {
            return false;
        }
        $item = $this->cache->get($this->url, $this->cacheLifeTime);
        if (!$item) {
            return false;
        }
        $this->content = $item->data;
        $this->info = (array)$item->meta;
        return true;
    }

    /**
     * @throws ClientException
     */
    public function execute(): static
    {
        $start = microtime(true);
        if ($this->fromCache()) {
            $this->duration = microtime(true - $start);
            return $this;
        }

        $this->prepare();
        $resp = curl_exec($this->handle);
        if ($resp === false) {
            throw new ClientException($this, curl_error($this->handle), curl_errno($this->handle));
        }
        $this->duration = microtime(true) - $start;

        if ($resp) {
            $this->content = $resp;
            $this->cache?->set($this->url, new CacheItem($resp, $this->getInfo()));
        }
        return $this;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function setCookiePath(string $cookiePath): void
    {
        $this->set(CURLOPT_COOKIEFILE, $cookiePath)
            ->set(CURLOPT_COOKIEJAR, $cookiePath);
    }

}