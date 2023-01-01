<?php

namespace Tivins\Core\API\Exceptions;

use Tivins\Core\Net\http\Status;

class BadRequestException extends APIException
{
    public function getStatus(): Status
    {
        return Status::BadRequest;
    }
}