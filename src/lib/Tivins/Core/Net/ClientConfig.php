<?php

namespace Tivins\Core\Net;

use Tivins\Core\Net\Http\ContentType;
use Tivins\Core\Net\Http\Header;
use Tivins\Core\Net\Http\Headers;
use Tivins\Core\Net\Http\Method;

class ClientConfig
{
    private array   $conf = [];
    private Headers $headers;

    public function __construct()
    {
        $this->headers = new Headers();
        $this->conf    = [
            CURLOPT_HTTPHEADER     => [],
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:' . ($v = rand(96, 108)) . '.0) Gecko/20100101 Firefox/' . $v . '.0',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE        => 0,
            CURLOPT_POST           => false,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_POSTFIELDS     => '',
            CURLOPT_ENCODING       => '',
        ];
    }

    protected function build(): array
    {
        $headerArray                    = $this->headers->getHeaders();
        $value                          = array_map(fn(string $k, string $v) => "$k: $v", array_keys($headerArray), array_values($headerArray));
        $this->conf[CURLOPT_HTTPHEADER] = $value;
        return $this->conf;
    }

    public function postData($data): static
    {
        $this->conf[CURLOPT_CUSTOMREQUEST] = Method::POST->value;
        $this->conf[CURLOPT_POSTFIELDS] = http_build_query($data);
        return $this;
    }

    public function postJSON($data): static
    {
        $this->conf[CURLOPT_CUSTOMREQUEST] = Method::POST->value;
        $this->conf[CURLOPT_POSTFIELDS]    = json_encode($data);
        $this->headers->setHeader(Header::ContentType, ContentType::JSON->value);
        return $this;
    }

    public function set(int $k, $v): static
    {
        $this->conf[$k] = $v;
        return $this;
    }

    public function getHeaders(): Headers {
        return $this->headers;
    }

    public function setHeaders(Headers $headers): static
    {
        $this->headers = $headers;
        return $this;
    }
}
