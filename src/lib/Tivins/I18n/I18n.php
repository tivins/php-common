<?php

namespace Tivins\I18n;

class I18n
{
    private array $data = [];

    public function add(string $key, string $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function addList(array $collection): static
    {
        foreach ($collection as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function get(string $key, string $default = ''): string
    {
        return $this->data[$key] ?? $default;
    }
}