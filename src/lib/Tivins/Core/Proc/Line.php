<?php

namespace Tivins\Core\Proc;

class Line
{
    public function __construct(public readonly string $string, public readonly string $char)
    {
    }
}
