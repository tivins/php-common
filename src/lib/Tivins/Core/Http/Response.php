<?php

namespace Tivins\Core\Http;

class Response
{
    public function __construct(
        public string $body = '',
        public Status $status = Status::OK,
        public ContentType $type = ContentType::HTML,
    )
    {
    }
}