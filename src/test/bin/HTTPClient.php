<?php

use Tivins\Core\Net\Client;
use Tivins\Core\Net\ClientAsync;
use Tivins\Core\Net\ClientException;
use Tivins\Core\Net\ClientMulti;
use Tivins\Core\Net\Http\ContentType;
use Tivins\Core\Net\Http\Header;
use Tivins\Core\Net\Http\Headers;
use Tivins\Core\System\Terminal;

require 'vendor/autoload.php';

$progressCallback = function(ClientMulti $client, float $duration) {
    Terminal::goUpClean(1);
    echo "$client => ".number_format($duration,1)."s\n";
};
$header = new Headers();
$header->setHeader(Header::Authorization, "Hello world");
// ---
testClients(
    new Client('https://httpbin.org/anything'),
    (new Client('https://httpbin.org/anything'))->setHeaders($header),
    (new Client('https://httpbin.org/anything'))->setHeaders($header)->postData(['yo'=>'lo']),
    (new Client('https://httpbin.org/anything'))->setHeaders($header)->postJSON(['yo'=>'lo']),
    (new ClientAsync('https://httpbin.org/anything'))->setProgressCallback($progressCallback),
);

function testClients(Client ...$clients): void{
    foreach ($clients as $client) {
        testClient($client);
    }

}
function testClient(Client $client): void
{
    echo "\n"; // Prevent first goUpClean();
    try {
        $client = $client->execute();
    } catch (ClientException $ex) {
        echo "$ex->client : " . $ex->getMessage() . "\n";
        return;
    }
    echo $client . ' ' . $client->getCode() . ' (' . strlen($client->getContent()) . ')', PHP_EOL;
    echo number_format($client->getDuration(), 1). "s", PHP_EOL;
    echo $client->getContent();
}
