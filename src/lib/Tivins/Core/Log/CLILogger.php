<?php

namespace Tivins\Core\Log;

use Tivins\Core\System\Terminal;

class CLILogger extends Logger
{
    private bool $decorated = true;

    private function colorLog(Level $level, string $str): string
    {
        if (! $this->decorated) {
            return $str;
        }
        return Terminal::decorate($level, $str);
    }

    public function write(Level $level, string $message, mixed ...$data)
    {
        echo $this->colorLog($level, sprintf("[ %-9s ] [ %s ] - %s\n", $level->name, date('c'), $message));
        if (! empty(array_filter($data))) echo "\e[90m" . " └─ " . json_encode($data) . "\e[0m" . PHP_EOL;
    }

    public function isDecorated(): bool
    {
        return $this->decorated;
    }

    public function setDecorated(bool $decorated): static
    {
        $this->decorated = $decorated;
        return $this;
    }


}