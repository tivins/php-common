<?php

namespace Tivins\Core\Net;

use CurlMultiHandle;
use Tivins\Core\Cache\Cache;
use Tivins\Core\Code\FastToString;
use Tivins\Core\Util;

class ClientMulti
{
    use FastToString;

    private CurlMultiHandle $handle;
    /** @var Client[] */
    private array      $clients              = [];
    private mixed      $callback             = null;
    private mixed      $progressCallback     = null;
    private float      $progress             = 0;
    private Cache|null $cache                = null;
    private int        $defaultCacheLifeTime = Cache::None;
    /**
     * @var int The number of microseconds to sleep between each idle-loop.
     * @see setMicroDelay()
     */
    private int        $uSleep               = 100000; // 100ms


    public function __construct(?Cache $cache = null, int $defaultCacheLifeTime = Cache::None)
    {
        $this->handle = curl_multi_init();
        $this->setCache($cache, $defaultCacheLifeTime);
    }

    public function __destruct()
    {
        curl_multi_close($this->handle);
    }


    public function setCache(?Cache $cache, int $defaultCacheLifeTime = Cache::None): static
    {
        $this->cache                = $cache;
        $this->defaultCacheLifeTime = $defaultCacheLifeTime;
        return $this;
    }

    /**
     * @param Client|string ...$clients A list of Clients or URL.
     * @return $this
     */
    public function addClients(Client|string ...$clients): static
    {
        foreach ($clients as $client) {
            if (is_string($client)) {
                $client = (new Client($client))
                    ->setCache($this->cache, $this->defaultCacheLifeTime);
            }
            $client->prepare();
            $handle             = $client->getHandle();
            $id                 = Util::getObjectID($handle);
            $this->clients[$id] = $client;
            if ($client->fromCache()) {
                continue;
            }
            curl_multi_add_handle($this->handle, $handle);
        }
        return $this;
    }

    /**
     * @return Client[]
     */
    public function getClients(): array
    {
        return $this->clients;
    }

    public function getClientByURL(string $url): Client|null
    {
        foreach ($this->clients as $client) {
            if ($client->getURL() == $url) {
                return $client;
            }
        }
        return null;
    }

    public function setReceiveCallback(callable|null $callback): static
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * `progressCallback(Client $client, float $duration)`
     *
     * @param callable|null $callback
     * @return $this
     */
    public function setProgressCallback(callable|null $callback): static
    {
        $this->progressCallback = $callback;
        return $this;
    }

    public function execute(): float
    {
        $clientsDone = [];
        $start       = microtime(true);
        do {
            $mrc = curl_multi_exec($this->handle, $active);
            if ($state = curl_multi_info_read($this->handle)) {
                $clientHandle = $state['handle'];
                $client       = $this->clients[Util::getObjectID($clientHandle)];
                $client->setFromMultipleContent();
                $clientsDone[]  = $client;
                $this->progress = count($clientsDone) / count($this->clients);
                if ($this->callback) {
                    ($this->callback)($this, $client);
                }
                curl_multi_remove_handle($this->handle, $clientHandle);
            }
            $this->progress = count($clientsDone) / count($this->clients);
            if ($this->progressCallback) {
                ($this->progressCallback)($this, microtime(true) - $start);
            }
            usleep($this->uSleep); // stop wasting CPU cycles and rest for a couple ms
        } while ($mrc == CURLM_CALL_MULTI_PERFORM || $active);
        return microtime(true) - $start;
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    public function getMicroDelay(): int
    {
        return $this->uSleep;
    }

    public function setMicroDelay(int $uSleep): void
    {
        $this->uSleep = $uSleep;
    }
}