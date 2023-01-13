<?php

namespace Tivins\Core\API\Exceptions;

use Exception;
use Tivins\Core\Net\Http\Status;

abstract class APIException extends Exception
{
    abstract public function getStatus(): Status;
}