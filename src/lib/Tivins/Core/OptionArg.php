<?php

namespace Tivins\Core;

class OptionArg
{
    public function __construct(
        private string  $short,
        private bool    $needValue = false,
        private ?string $long = null)
    {
    }

    public function getShort(): string
    {
        return $this->short;
    }

    public function requireValue(): bool
    {
        return $this->needValue;
    }

    public function getLong(): ?string
    {
        return $this->long;
    }
}