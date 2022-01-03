<?php

namespace Tivins\Core\Log;

use Tivins\Core\System\Terminal;

class CLILogger extends Logger
{
    private bool $decorate = true;

    function colorLog(Level $level, string $str): string
    {
        if (! $this->decorate) {
            return $str;
        }
        return Terminal::decorate($level, $str);
    }


    public function write(Level $level, string $message, mixed ...$data)
    {
        echo $this->colorLog($level, sprintf("[ %-9s ] [ %s ] - %s\n", $level->name, date('c'), json_encode($message)));
        if (! empty(array_filter($data))) echo "\e[90m" . " └─ " . json_encode($data) . "\e[0m" . PHP_EOL;
    }

    public function isDecorate(): bool
    {
        return $this->decorate;
    }

    public function setDecorate(bool $decorate): static
    {
        $this->decorate = $decorate;
        return $this;
    }


}