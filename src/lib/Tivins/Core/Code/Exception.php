<?php

namespace Tivins\Core\Code;

use Tivins\Core\Http\Status;

class Exception extends \Exception
{
    public function __construct(string $publicMessage = '',
                                private string $privateMessage = '',
                                private Status $status = Status::InternalServerError
    )
    {
        parent::__construct($publicMessage);
    }

    /**
     * @return string
     */
    public function getPrivateMessage(): string
    {
        return $this->privateMessage;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }
}