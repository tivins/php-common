<?php

namespace Tivins\Core\Log;

class CLILogger extends Logger
{
    private bool $decorate = true;

    function colorLog(Level $level, string $str): string
    {
        if (! $this->decorate) {
            return $str;
        }
        return match ($level) {
            Level::DANGER   => "\033[31m$str\033[0m",
            Level::SUCCESS  => "\033[32m$str\033[0m",
            Level::WARNING  => "\033[33m$str\033[0m",
            Level::INFO     => "\033[36m$str\033[0m",
            Level::DEBUG    => "\033[37m$str\033[0m",
            default         => $str,
        };
    }

    public function write(Level $level, string $message, mixed $data = null)
    {
        echo $this->colorLog($level, sprintf("[ %-9s ] [ %s ] - %s\n", $level->name, date('c'), json_encode($message)));
        if (!is_null($data)) echo " └─ " . json_encode($data) . PHP_EOL;
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