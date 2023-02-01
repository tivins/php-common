<?php

namespace Tivins\Dev\JS;

class Console
{
    public function log(mixed ...$args): void
    {
        var_dump($args);
    }
}