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
     * @param Status $status
     * @return $this
     */
    public function setStatus(Status $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param ContentType $type
     * @return $this
     */
    public function setContentType(ContentType $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return ContentType
     */
    public function getContentType(): ContentType
    {
        return $this->type;
    }
}
