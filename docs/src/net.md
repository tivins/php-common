

## Core/Network/HTTP

NB: outputs are stored at pre-commit time.

## HTTP Client (`Tivins\Core\Net\Client`)

* [Minimal](#minimal)
* [Post + Token Bearer](#post-token-bearer)
* [Asynchronous](#asynchronous)
* [Multiple](#multiple-calls)
* [Using Cache](#using-cache)


### Minimal

{{{ run | src/test/bin/HttpClientBasic.php | code,output }}}

### Post + Token Bearer

{{{ run | src/test/bin/HttpClientAuth.php | code,output }}}

### Asynchronous

Cet exemple démontre comment effectuer une requête HTTP de manière asynchrone. 
Appeler la méthode `setProgressCallback()` pour fournir la callback qui sera appelée durant le traitement.

{{{ run | src/test/bin/HttpClientAsync.php | code,output,cinema }}}

### Multiple calls

{{{ run | src/test/bin/HttpClientMulti.php | code,output }}}

### Using cache

...todo.