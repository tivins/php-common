<?php

namespace Tivins\Core\Net\Http;

class MetaElement
{
    private Headers $headers;
    private string  $body = '';

    public function __construct()
    {
        $this->headers = new Headers();
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

}