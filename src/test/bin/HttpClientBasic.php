<?php

use Tivins\Core\Net\{Client, ClientException};

require 'vendor/autoload.php';

try {
    $client = (new Client('https://httpbin.org/anything'))->execute();
    echo $client . ' ' . $client->getCode() . ' (' . strlen($client->getContent()) . ')' . PHP_EOL;
    print_r($client->getContentAsJSON());
}
catch (Exception|ClientException $ex) {
    exit($ex->client . ' : ' . $ex->getMessage() . "\n");
}

