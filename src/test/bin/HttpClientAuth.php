<?php

use Tivins\Core\Net\Client;
use Tivins\Core\Net\ClientException;
use Tivins\Core\Net\Http\Header;
use Tivins\Core\Net\Http\Headers;

require 'vendor/autoload.php';

$token = 'a-token-from-elsewhere';

$headers = (new Headers())
    ->setHeader(Header::Authorization, 'Bearer ' . $token);

try {
    $client = (new Client('https://httpbin.org/anything'))
        ->setHeaders($headers)
        ->postJSON(['yo' => 'lo'])
        ->execute();
}
catch (ClientException $e) {
    exit($e->client . ' ' . $e->getMessage() . "\n");
}

echo $client . ' ' . $client->getCode() . ' (' . strlen($client->getContent()) . ')', PHP_EOL;
echo $client->getContent(), PHP_EOL;
