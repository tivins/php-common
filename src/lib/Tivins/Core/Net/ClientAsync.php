<?php

namespace Tivins\Core\Net;

use Tivins\Core\Cache\Cache;

/**
 * Asynchronous version of Client that use ClientMulti.
 */
class ClientAsync extends Client
{
    private ClientMulti $multi;

    public function __construct(string $url, ?Cache $cache = null, int $cacheLifeTime = Cache::None)
    {
        parent::__construct($url, $cache, $cacheLifeTime);
        $this->multi = new ClientMulti($cache, $cacheLifeTime);
    }

    public function setProgressCallback(callable|null $callback): static
    {
        $this->multi->setProgressCallback($callback);
        return $this;
    }

    public function execute(): static
    {
        $this->multi->addClients($this);
        $this->duration = $this->multi->execute();
        return $this;
    }
}
