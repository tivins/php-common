# PHP-Common

## Install

```shell
composer require tivins/php-common dev-main
```

## Content

* [HTTP Client](/php-common/net)
* [CLI Options Managment](/php-common/cli)
----

### Files

```php
$content = File::load($filename);
if ($content === false) { /* catch error */ }
```
```php
if (! File::save($filename, $data)) { /* catch error */ }
```