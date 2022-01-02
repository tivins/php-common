<?php

namespace Tivins\Core\Proc;

class Proc
{
    public array $command;
    public array $status;
    public int $close;
    public string $stdout;
    public string $stderr;
    public int $started;
    public int $ended;

    public function hasError(): bool
    {
        return !empty($this->stderr);
    }

    /**
     * @param Command $command
     * @return Proc
     */
    public static function run(Command $command): Proc
    {
        $resource = proc_open($command->get(), [
            ['pipe', 'r'], // stdin
            ['pipe', 'w'], // stdout
            ['pipe', 'w'], // stderr
        ], $pipes);

        $proc = new Proc();
        $proc->command = $command->get();
        $proc->started = microtime(true);
        $proc->stdout = stream_get_contents($pipes[1]);
        $proc->stderr = stream_get_contents($pipes[2]);
        $proc->status = proc_get_status($resource);
        $proc->close = proc_close($resource);
        $proc->ended = microtime(true);
        return $proc;
    }

}