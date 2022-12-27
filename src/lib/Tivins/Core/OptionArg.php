<?php

namespace Tivins\Core;

class OptionArg
{
    private string $value;

    public function __construct(
        public readonly string  $long,
        public readonly bool $requireValue = false,
        public readonly string|null $short = null,
    )
    {
    }

    public function getId(): string
    {
        return ($this->long) . '|' . ($this->short ?? '');
    }

}
