<?php

use Tivins\Core\Net\ClientAsync;
use Tivins\Core\Net\ClientException;
use Tivins\Core\Net\ClientMulti;

require 'vendor/autoload.php';

$client = (new ClientAsync('https://httpbin.org/anything'))
    ->setProgressCallback(function (ClientMulti $client, float $duration) {
        echo "$client => " . number_format($duration, 1) . "s\n";
    })
    ->postJSON(['yo' => 'lo']);

try {
    $client->execute();
}
catch (ClientException $e) {
    exit($e->client . ' ' . $e->getMessage() . "\n");
}

echo $client . ' ' . $client->getCode() . ' (' . strlen($client->getContent()) . ')', PHP_EOL,
$client->getContent(), PHP_EOL;
