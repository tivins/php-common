<?php

namespace Tivins\Core;

use Attribute;
use Tivins\Core\Http\Method as HTTPMethod;

#[Attribute(Attribute::TARGET_METHOD)]
class APIAccess
{
    public function __construct(public HTTPMethod $method, public string $permission)
    {
    }
}