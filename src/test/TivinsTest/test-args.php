<?php

require __dir__ . '/../../../vendor/autoload.php';

$data = (new \Tivins\Core\OptionsArgs())
    ->add(new \Tivins\Core\OptionArg('h',true, 'help'))
    ->parse(['h'=>null]);

var_dump($data);