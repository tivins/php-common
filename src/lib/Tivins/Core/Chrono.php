<?php

namespace Tivins\Core;

class Chrono
{
    private float $tick;

    public function start()
    {
        $this->tick = microtime(true);
    }

    public function step(): float
    {
        return microtime(true) - $this->tick;
    }
}