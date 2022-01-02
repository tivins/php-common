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
        return self::decorate($level, $str);
    }

    public static function decorateSuccess(string $str): string { return self::decorate(Level::SUCCESS, $str); }
    public static function decorateDanger(string $str): string { return self::decorate(Level::DANGER, $str); }

    public static function decorate(Level $level, string $str): string
    {
        return match ($level) {
            Level::DANGER   => "\033[31m$str\033[0m",
            Level::SUCCESS  => "\033[32m$str\033[0m",
            Level::WARNING  => "\033[33m$str\033[0m",
            Level::INFO     => "\033[36m$str\033[0m",
            Level::DEBUG    => "\033[37m$str\033[0m",
            default         => $str,
        };
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