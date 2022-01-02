<?php

namespace Tivins\Core\Log;

abstract class Logger
{
    private Level $level = Level::DANGER;

    final public function log(Level $level, string $message, mixed ...$data)
    {
        if ($level->value > $this->level->value) {
            return;
        }
        $this->write($level, $message, $data);
    }

    abstract protected function write(Level $level, string $message, mixed ...$data);

    final public function danger(string $message, mixed ...$data) {
        $this->log(Level::DANGER, $message, ...$data);
    }
    final public function info(string $message, mixed ...$data) {
        $this->log(Level::INFO, $message, ...$data);
    }
    final public function warning(string $message, mixed ...$data) {
        $this->log(Level::WARNING, $message, ...$data);
    }
    final public function success(string $message, mixed ...$data) {
        $this->log(Level::SUCCESS, $message, ...$data);
    }
    final public function debug(string $message, mixed ...$data) {
        $this->log(Level::DEBUG, $message, ...$data);
    }


    /**
     * @return Level
     */
    public function getLevel(): Level
    {
        return $this->level;
    }

    public function setLevel(Level $level): static
    {
        $this->level = $level;
        return $this;
    }
}