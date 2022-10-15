<?php

namespace Tivins\Core\Routing;

abstract class Controller
{
    abstract public function query(array $args);
}