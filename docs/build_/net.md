

## Core/Network/HTTP

NB: outputs are stored at pre-commit time.

## HTTP Client (`Tivins\Core\Net\Client`)

* [Minimal](#minimal)
* [Post + Token Bearer](#post--token-bearer)
* [Asynchronous](#asynchronous)
* [Multiple](#multiple-calls)
* [Using Cache](#using-cache)


### Minimal

```php
<?php

use Tivins\Core\Net\{ Client, ClientException };

require 'vendor/autoload.php';

try {
    $client = (new Client('https://httpbin.org/anything'))->execute();
}
catch (ClientException $ex) {
    exit($ex->client . ' : ' . $ex->getMessage() . "\n");
}

echo $client . ' ' . $client->getCode() . ' (' . strlen($client->getContent()) . ')' . PHP_EOL;
print_r($client->getContentAsJSON());

```

<details><summary>Output</summary>

<pre>
Tivins\Core\Net\Client#4 200 (542)
stdClass Object
(
    [args] => stdClass Object
        (
        )

    [data] => 
    [files] => stdClass Object
        (
        )

    [form] => stdClass Object
        (
        )

    [headers] => stdClass Object
        (
            [Accept] => */*
            [Accept-Encoding] => deflate, gzip, br
            [Content-Length] => 0
            [Content-Type] => application/x-www-form-urlencoded
            [Host] => httpbin.org
            [User-Agent] => Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:107.0) Gecko/xx.xx.xx.xx Firefox/107.0
            [X-Amzn-Trace-Id] => Root=1-63cbb24f-187ff1dfxx.xx.xx.xx
        )

    [json] => 
    [method] => GET
    [origin] => xx.xx.xx.xx
    [url] => https://httpbin.org/anything
)

</pre>
</details>



### Post + Token Bearer

```php
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

```

<details><summary>Output</summary>

<pre>
Tivins\Core\Net\Client#21 200 (611)
{
  "args": {}, 
  "data": "{\"yo\":\"lo\"}", 
  "files": {}, 
  "form": {}, 
  "headers": {
    "Accept": "*/*", 
    "Accept-Encoding": "deflate, gzip, br", 
    "Authorization": "Bearer a-token-from-elsewhere", 
    "Content-Length": "11", 
    "Content-Type": "application/json", 
    "Host": "httpbin.org", 
    "User-Agent": "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:99.0) Gecko/xx.xx.xx.xx Firefox/99.0", 
    "X-Amzn-Trace-Id": "Root=1-63cbbxx.xx.xx.xxbdxx.xx.xx.xx"
  }, 
  "json": {
    "yo": "lo"
  }, 
  "method": "POST", 
  "origin": "xx.xx.xx.xx", 
  "url": "https://httpbin.org/anything"
}


</pre>
</details>



### Asynchronous

```php
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

```

<details><summary>Output</summary>

<pre>
Tivins\Core\Net\ClientMulti#5 => 0.0s
Tivins\Core\Net\ClientMulti#5 => 0.1s
Tivins\Core\Net\ClientMulti#5 => 0.2s
Tivins\Core\Net\ClientMulti#5 => 0.3s
Tivins\Core\Net\ClientMulti#5 => 0.4s
Tivins\Core\Net\ClientMulti#5 => 0.5s
Tivins\Core\Net\ClientMulti#5 => 0.6s
Tivins\Core\Net\ClientAsync#4 200 (558)
{
  "args": {}, 
  "data": "{\"yo\":\"lo\"}", 
  "files": {}, 
  "form": {}, 
  "headers": {
    "Accept": "*/*", 
    "Accept-Encoding": "deflate, gzip, br", 
    "Content-Length": "11", 
    "Content-Type": "application/json", 
    "Host": "httpbin.org", 
    "User-Agent": "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:101.0) Gecko/xx.xx.xx.xx Firefox/101.0", 
    "X-Amzn-Trace-Id": "Root=1-63cbbxx.xx.xx.xxbaa64daxx.xx.xx.xxf"
  }, 
  "json": {
    "yo": "lo"
  }, 
  "method": "POST", 
  "origin": "xx.xx.xx.xx", 
  "url": "https://httpbin.org/anything"
}


</pre>
</details>



### Multiple calls

```php
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



```

<details><summary>Output</summary>

<pre>
- #45 : Tivins\Core\Net\Client#43, Code: 200, Size: 1256 bytes
- #46 : Tivins\Core\Net\Client#5, Code: 200, Size: 558 bytes

</pre>
</details>



### Using cache

...todo.