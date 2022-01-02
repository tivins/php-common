<?php

namespace Tivins\Core\Log;

class CLILogger extends Logger
{
    public function log(Level $level, string $message, $data = null)
    {
        printf("[ %-9s ] [ %s ] - %s\n", $level->name, date('c'), json_encode($message));
        if ($data) echo " └─ " . json_encode($data) . PHP_EOL;
    }
}