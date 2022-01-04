<?php

namespace Tivins\Core\Proc;

use Exception;

class ProcError extends Exception
{
    public function __construct(public ProcInfo $proc, string $message = '')
    {
        parent::__construct($message);
    }
}