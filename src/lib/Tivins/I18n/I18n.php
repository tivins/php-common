<?php

namespace Tivins\I18n;

class I18n
{
    private array $data = [];

    public function __construct(array $collection = [])
    {
        $this->addList($collection);
    }

    public function addList(array $collection): static
    {
        if (!empty($collection)) {
            $this->data = $collection + $this->data;
        }
        return $this;
    }

    public function add(string $key, string $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get(string $key, string $default = ''): string
    {
        return $this->data[$key] ?? $default;
    }
}