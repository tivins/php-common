<?php

namespace Tivins\Core\API\Exceptions;

use Tivins\Core\Net\Http\Status;

class AuthorizationException extends APIException
{
    public function getStatus(): Status
    {
        return Status::Unauthorized;
    }
}