<?php

use Tivins\Core\Net\Client;
use Tivins\Core\Net\ClientException;
use Tivins\Core\Net\ClientMulti;

require 'vendor/autoload.php';

$clients = new ClientMulti();
// Add clients: URL or Client
$clients->addClients(
    'https://example.com/',
    (new Client('https://httpbin.org/anything'))->postJSON(['yo' => 'lo']),
);

$duration = $clients->execute();
foreach ($clients->getClients() as $k => $client) {
    printf(
        "- #%d : %s, Code: %d, Size: %d bytes\n",
        $k, $client, $client->getCode(), strlen($client->getContent())
    );
}


