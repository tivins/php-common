<?php

namespace Tivins\Core\Log;

abstract class Logger
{
    private Level $level = Level::DANGER;

    abstract  public function write(Level $level, string $message, object|null $data);

    final public function log(Level $level, string $message, mixed $data = null)
    {
        if ($level->value > $this->level->value) {
            return;
        }
        $this->write($level, $message, $data);
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