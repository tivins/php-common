<?php

namespace Tivins\Core\Routing;

use Tivins\Core\Http\Response;

abstract class Controller
{
    abstract public function query(array $args): Response;
}