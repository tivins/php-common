<?php

namespace Tivins\Core\Http;

/**
 *
 */
class Response
{
    /**
     * @param string $body
     * @param Status $status
     * @param ContentType $type
     */
    public function __construct(
        public string      $body = '',
        public Status      $status = Status::OK,
        public ContentType $type = ContentType::HTML,
    )
    {
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->type;
    }

    /**
     * @return ContentType
     */
    public function getType(): ContentType
    {
        return $this->type;
    }

    /**
     * @param ContentType $type
     * @return static
     */
    public function setType(ContentType $type): static
    {
        $this->type = $type;
        return $this;
    }
}
