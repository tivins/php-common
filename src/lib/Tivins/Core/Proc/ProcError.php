<?php

namespace Tivins\Core\Proc;

use Exception;

class ProcError extends Exception
{
    public function __construct(public Proc $proc, string $message = '')
    {
        parent::__construct($message);
    }
}