<?php

use Tivins\Core\System\Terminal;

require 'vendor/autoload.php';

$a = array(
    'test'
    => 2
);

var_dump($a);
Terminal::sleep(1);
$a['test']++;
var_dump($a);
Terminal::sleep(.2);
