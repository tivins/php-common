<?php

namespace Tivins\Core;

class OptionArg
{
    private string $value;

    public function __construct(
        private ?string  $short = null,
        private bool    $needValue = false,
        private ?string $long = null,
        private mixed   $default = null)
    {
    }

    public function getId(): string
    {
        return ($this->long ?? '') . '|' . ($this->short ?? '');
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

    public function getDefault(): mixed
    {
        return $this->default;
    }
}