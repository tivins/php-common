<?php

namespace Tivins\Core;

/**
 * Usage:
 *
 * $chrono = (new Chrono)->start();
 * // do some stuff
 * $duration = $chrono->step();
 */
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