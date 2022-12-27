<?php

namespace Tivins\Core;

class Color {
    public function __construct(
        public int $red,
        public int $green,
        public int $blue,
    )
    {
    }

    public function toHexString(): string
    {
        return sprintf('%02x%02x%02x', $this->red, $this->green, $this->blue);
    }
}