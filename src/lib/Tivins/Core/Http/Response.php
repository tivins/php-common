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

    public function getBody(): string {
        return $this->body;
    }

    public function getStatus(): Status {
        return $this->status;
    }

    public function getContentType(): ContentType {
        return $this->type;
    }

}