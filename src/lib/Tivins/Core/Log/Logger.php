<?php

namespace Tivins\Core\Log;

abstract class Logger
{
    abstract public function log(Level $level, string $message, object|null $data);
}