<?php

namespace Tivins\Core\Code;

use Tivins\Core\Net\Http\Status;

class Exception extends \Exception
{
    public function __construct(public readonly string $publicMessage = '',
                                public readonly string $privateMessage = '',
                                public readonly Status $status = Status::InternalServerError
    )
    {
        parent::__construct($publicMessage);
    }
}