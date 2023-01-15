<?php

namespace Tivins\Core\API\Exceptions;

use Throwable;

class NoEndPointException extends BadRequestException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('No endpoint', $code, $previous);
    }
}