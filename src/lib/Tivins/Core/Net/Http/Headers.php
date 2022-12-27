<?php

namespace Tivins\Core\Net\Http;

class Headers
{
    private array $headers = [];

    public function setHeader(Header $header, string $value): static
    {
        return $this->setCustomHeader($header->value, $value);
    }

    public function setCustomHeader(string $header, string $value): static
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @return array[string]=string
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array [
     *  "Header1: Value1",
     *  "Header2: Value2",
     *  ...
     * ]
     * @see CURLOPT_HTTPHEADER
     */
    public function getHeadersRaw(): array
    {
        return array_map(
            fn(string $k, string $v) => "$k: $v",
            array_keys($this->headers),
            array_values($this->headers)
        );
    }

}