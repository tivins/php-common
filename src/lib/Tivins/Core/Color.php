<?php

namespace Tivins\Core;

class Color
{
    public function __construct(
        public int $red,
        public int $green,
        public int $blue,
    )
    {
    }

    public function clamp(): static
    {
        $this->red   = max(0, min($this->red, 0xff));
        $this->green = max(0, min($this->green, 0xff));
        $this->blue  = max(0, min($this->blue, 0xff));
        return $this;
    }

    public function format(ColorFormat $format = ColorFormat::HEX): string
    {
        return sprintf($format->value, $this->red, $this->green, $this->blue);
    }
}