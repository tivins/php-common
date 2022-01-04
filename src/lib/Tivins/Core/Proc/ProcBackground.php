<?php

namespace Tivins\Core\Proc;

use Tivins\Core\Log\Level;
use Tivins\Core\System\Terminal;

class ProcBackground extends Process
{
    private string  $str      = '⠉⠛⠿⣿⣶⣤⣀⣤⣶⣿⠿⠛';
    private int     $len;
    private string  $message;
    private ?string $endMessage;
    private ?string $failMessage;

    private array $display = [self::STDOUT => false, self::STDERR => true];
    private array $buffers = [self::STDOUT => '', self::STDERR => ''];

    public function __construct(string $message, ?string $endMessage = null, ?string $failMessage = null)
    {
        $this->message    = $message;
        $this->endMessage = $endMessage;
        $this->failMessage = $failMessage;
        $this->len        = mb_strlen($this->str);
    }

    public function onStart()
    {
        // stream_set_blocking($this->proc->pipes[1], false);
        // stream_set_blocking($this->proc->pipes[2], false);
    }

    public function wrapPartialContent(int $fd): string
    {
        $pos = strrpos($this->buffers[$fd], "\n");
        $out = substr($this->buffers[$fd], 0, $pos + 1);
        $this->buffers[$fd] = substr($this->buffers[$fd], $pos + 1);
        return $out;
    }

    private function getLoaderChar(): string
    {
        static $counter;
        if (!isset($counter)) $counter = $this->proc->started;
        $pos = round((microtime(true)-$this->proc->started)*10) % $this->len;
        return mb_substr($this->str, $pos, 1);
    }

    public function onUpdate(array $status, array $received)
    {
        $this->buffers[self::STDOUT] .= $received[self::STDOUT];
        $this->buffers[self::STDERR] .= $received[self::STDERR];

        $stdout = $this->wrapPartialContent(self::STDOUT);
        $stderr = $this->wrapPartialContent(self::STDERR);

        echo Terminal::getClearLine();
        if ($this->display[self::STDOUT]) echo Terminal::decorateInfo($stdout);
        if ($this->display[self::STDERR]) echo Terminal::decorateDanger($stderr);

        $loader = $this->getLoaderChar()
            . ' [' . number_format(microtime(true) - $this->proc->started, 1) . 's] '
            ;

        echo "\r" . Terminal::decorateSuccess($loader) . $this->message . "…";
    }

    public function onFinish()
    {
        echo Terminal::getClearLine();

        $err = $this->proc->hasError();
        $loadChar = $err ? 'x' : '✓'; // mb_substr($this->str, $this->full, 1);
        $message = $err && $this->failMessage ? $this->failMessage : ($this->endMessage ?? $this->message);
        $timeStr  = ' in [' . number_format(microtime(true) - $this->proc->started, 3) . 's]';

        echo Terminal::getClearLine(); // Erase line width animation
        echo Terminal::decorate($err ? Level::DANGER : Level::INFO,
                $loadChar . ' ' . $message) . $timeStr . ".\n";
    }

    /**
     * @param bool $showStderr
     * @return ProcBackground
     */
    public function setShowStderr(bool $showStderr): ProcBackground
    {
        $this->showStderr = $showStderr;
        return $this;
    }

    /**
     * @param bool $showStdout
     * @return ProcBackground
     */
    public function setShowStdout(bool $showStdout): ProcBackground
    {
        $this->display[self::STDOUT] = $showStdout;
        return $this;
    }
}
