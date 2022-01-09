<?php

namespace Tivins\Core\Proc;

class Process
{
    public const STDIN  = 0;
    public const STDOUT = 1;
    public const STDERR = 2;

    public const FEED_MEMORY = 'pipe';
    public const FEED_FILE   = 'file';

    protected ProcInfo|null $proc = null;

    private array $descriptors = [
        self::STDIN  => ['pipe', 'r'],
        self::STDOUT => ['pipe', 'w'],
        self::STDERR => ['pipe', 'w'],
    ];

    /**
     * @var array Stream according to descriptors.
     * @see $descriptors
     */
    protected array $pipes = [];

    /**
     * @var string|null The initial working dir for the command. This must be an absolute directory path,
     *  or null if you want to use the default value (the working dir of the current PHP process).
     */
    private string|null $workingDir = null;

    /**
     * @var array<string,string>|null
     */
    private ?array $envVars = null;

    /**
     * @param Command $command The command to run.
     *
     * @param int $asyncFreq
     *      Interval for asynchronous call, defined in microseconds (eg: 1s = 1000000µs).
     *      Make the call asynchronous, if $asyncFreq is greater than zero.
     *
     * @param string|null $stdin Data to give to process through stdin.
     *      This could be used to pass secrets to the process.
     */
    public function run(Command $command, int $asyncFreq = 0, ?string $stdin = null): ProcInfo
    {
        $this->proc = new ProcInfo();
        $this->proc->command = $command->get();
        $this->proc->started = microtime(true);

        $resource = proc_open(
            $command->get(),
            $this->descriptors,
            $this->pipes,
            $this->workingDir,
            $this->envVars
        );

        if (!$resource) {
            $this->proc->status = false;
            return $this->proc;
        }

        if ($stdin !== null) {
            fwrite($this->pipes[self::STDIN], $stdin);
            fclose($this->pipes[self::STDIN]);
        }

        $this->onStart();
        if ($asyncFreq > 0)
        {
            stream_set_blocking($this->pipes[self::STDOUT], false);
            stream_set_blocking($this->pipes[self::STDERR], false);

            while (($status = proc_get_status($resource))['running'])
            {
                $received = $this->getDataFromStreams();
                $this->onUpdate($status, $received);
                usleep($asyncFreq);
            }
        }
        $received = $this->getDataFromStreams();
        $status = proc_get_status($resource);
        $this->onUpdate($status, $received);

        $this->proc->close  = proc_close($resource);
        $this->proc->ended  = microtime(true);
        $this->onFinish();
        return $this->proc;
    }

    private function getDataFromStreams(): array
    {
        $received = [
            self::STDOUT => stream_get_contents($this->pipes[self::STDOUT]),
            self::STDERR => stream_get_contents($this->pipes[self::STDERR]),
        ];
        $this->proc->stdout .= $received[self::STDOUT] ?: '';
        $this->proc->stderr .= $received[self::STDERR] ?: '';
        return $received;
    }

    public function setEnvVars(array $vars)
    {
        $this->envVars = $vars;
    }

    /**
     * Called just after the process opening.
     */
    public function onStart()
    {
    }


    /**
     * Called on every step if the call is asynchronous.
     *
     * Ex:
     * ```php
     * echo round(microtime(true) - $this->proc->started, 3), "\r";
     * ```
     */
    public function onUpdate(array $status, array $received)
    {
    }

    /**
     * Called when the process was finished.
     *
     * Ex:
     * ```php
     * echo Terminal::getClearLine();
     * ```
     */
    public function onFinish()
    {
    }
}