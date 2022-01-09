<?php

namespace Tivins\Core\Proc;

class ProcInfo
{
    public array  $command = [];
    public bool   $status  = true;
    public int    $close   = -1; // exitCode
    public string $stdout  = '';
    public string $stderr  = '';
    public float  $started = 0;
    public float  $ended   = 0;

    public function hasError(): bool
    {
        return !empty($this->stderr);
    }
}