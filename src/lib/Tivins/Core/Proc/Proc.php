<?php

namespace Tivins\Core\Proc;

class Proc
{
    public array  $command;
    public array  $status;
    public int    $close;
    public string $stdout = '';
    public string $stderr = '';
    public int    $started;
    public int    $ended;

    /**
     * @var resource[]
     */
    public array  $pipes;

    /**
     * @param Command $command
     * @param ProcHooks|null $hooks
     * @return Proc|null
     */
    public static function run(Command $command, ProcHooks|null $hooks = null): Proc|null
    {
        $resource = proc_open($command->get(), [
            ['pipe', 'r'], // stdin
            ['pipe', 'w'], // stdout
            ['pipe', 'w'], // stderr
        ], $pipes);

        if (! is_resource($resource)) {
            return null;
        }

        $proc = new Proc();
        $proc->pipes =& $pipes;
        $hooks?->setProc($proc);
        $hooks?->onStart();
        $proc->command = $command->get();
        $proc->started = microtime(true);
        while ($hooks && ($proc->status = proc_get_status($resource))['running']) {
            $hooks->onUpdate();
            usleep($hooks->getCallbackFrequency());
        }
        $proc->stdout  .= stream_get_contents($pipes[1]);
        $proc->stderr  .= stream_get_contents($pipes[2]);
        $proc->status  = proc_get_status($resource);
        $proc->close   = proc_close($resource);
        $proc->ended   = microtime(true);
        $hooks?->onFinished();
        return $proc;
    }

    public function hasError(): bool
    {
        return !empty($this->stderr);
    }

}