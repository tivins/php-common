<?php

namespace Tivins\Core;

class Chrono
{
    private float $tick;

    public function start(): static
    {
        $this->tick = microtime(true);
        return $this;
    }

    public function step(): float
    {
        return microtime(true) - $this->tick;
    }
}