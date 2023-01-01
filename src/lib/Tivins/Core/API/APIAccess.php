<?php

namespace Tivins\Core\API;

use Attribute;
use Tivins\Core\Net\Http\Method;

#[Attribute(Attribute::TARGET_METHOD|Attribute::TARGET_CLASS)]
class APIAccess
{
    public function __construct(
        public string $service,
        public Method $method,
        public string $permission = 'public')
    {
    }
}