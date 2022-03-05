<?php

namespace Tivins\Core\Code;

class Exception extends \Exception
{
    public function __construct(string $publicMessage = '', private string $privateMessage = '')
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
}