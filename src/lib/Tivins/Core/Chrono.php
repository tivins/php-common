<?php

namespace Tivins\Core;

/**
 * Usage:
 *
 * ```php
 * $chrono = (new Chrono)->start();
 * sleep(1);
 * $duration = $chrono->get(); // ~1.0
 * sleep(1);
 * $duration = $chrono->get(); // ~2.0
 * sleep(1);
 * $duration = $chrono->getReset(); // ~3.0
 * sleep(1);
 * $duration = $chrono->get(); // ~1.0
 * ```
 */
class Chrono
{
    private float $tick;

    public function start(): static
    {
        $this->tick = microtime(true);
        return $this;
    }

    public function get(): float
    {
        return microtime(true) - $this->tick;
    }

    public function getReset(): float
    {
        $duration = $this->get();
        $this->start();
        return $duration;
    }
}