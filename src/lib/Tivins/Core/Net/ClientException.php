<?php

namespace Tivins\Core\Net;

use Throwable;

class ClientException extends \Exception
{
    public readonly Client $client;

    public function __construct(Client $client, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->client = $client;
        parent::__construct($message, $code, $previous);
    }
}