<?php

namespace Tivins\Core\API\Exceptions;

use Tivins\Core\Net\http\Status;

class NotFoundException extends APIException
{
    public function getStatus(): Status
    {
        return Status::NotFound;
    }
}