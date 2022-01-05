<?php

namespace Tivins\Core\Proc;

class Command
{
    private array $cmd = [];

    public function __construct(string ...$args)
    {
        $this->add(...$args);
    }

    public function add(string ...$args): static
    {
        $this->cmd = array_merge($this->cmd, $args);
        return $this;
    }

    public function addCommand(Command|null $cmd): static
    {
        if (is_null($cmd)) return $this;
        $this->add(...$cmd->get());
        return $this;
    }

    public function get(): array
    {
        return $this->cmd;
    }
}