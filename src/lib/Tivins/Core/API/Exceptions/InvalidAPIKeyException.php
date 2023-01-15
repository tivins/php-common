<?php

namespace Tivins\Core\API\Exceptions;

use Throwable;

class InvalidAPIKeyException extends AuthorizationException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Invalid API Key', $code, $previous);
    }
}