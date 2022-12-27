<?php

namespace Tivins\Core\Proc;

use Tivins\Core\Log\Level;
use Tivins\Core\System\Terminal;

class ProcBackground extends Process
{
    private string  $str = '⠉⠛⠿⣿⣶⣤⣀⣤⣶⣿⠿⠛';
    private int     $len;
    private string  $message;
    private ?string $endMessage;
    private ?string $failMessage;

    private array $display = [self::STDOUT => false, self::STDERR => true];
    private array $buffers = [self::STDOUT => '', self::STDERR => ''];

    public function __construct(string $message, ?string $endMessage = null, ?string $failMessage = null)
    {
        $this->message     = $message;
        $this->endMessage  = $endMessage;
        $this->failMessage = $failMessage;
        $this->len         = mb_strlen($this->str);
    }

    private function displayLines(string $logs): string
    {
        $lines = array_filter(explode("\n", $logs) ?: []);
        if (empty($logs)) return '';
        $out = [];
        foreach ($lines as $line) {
            $write = $line;
            if (strlen($line) > Terminal::width() - 10) {
                $partLen = floor((Terminal::width() - 10 - 3) / 2);
                $write   = substr($line, 0, $partLen) . '...' . substr($line, -$partLen);
            }
            $out[] = "  | " . $write;
        }
        return join("\n", $out) . "\n";
    }

    public function onUpdate(array $status, array $received): void
    {
        $this->buffers[self::STDOUT] .= $received[self::STDOUT];
        $this->buffers[self::STDERR] .= $received[self::STDERR];
        // $lines = array_map(fn($info) => new Line($info[0], substr($proc->stderr, $info[1] - 1, 1)), $lines);

        $stdout = $this->wrapPartialContent(self::STDOUT);
        $stderr = $this->wrapPartialContent(self::STDERR);

        echo Terminal::getClearLine();
        if ($this->display[self::STDOUT]) echo Terminal::decorateInfo($this->displayLines($stdout));
        if ($this->display[self::STDERR]) echo Terminal::decorateDanger($this->displayLines($stderr));

        $loader = $this->getLoaderChar()
            . ' [' . number_format(microtime(true) - $this->proc->started, 1) . 's] ';

        echo "\r" . Terminal::decorateSuccess($loader) . $this->message . "…";
    }

    public function onFinish(): void
    {
        echo Terminal::getClearLine();

        $err      = $this->proc->hasError();
        $loadChar = $err ? 'x' : '✓'; // mb_substr($this->str, $this->full, 1);
        $message  = $err && ($this->failMessage ? $this->failMessage : ($this->endMessage ?? $this->message));
        $timeStr  = ' in [' . number_format(microtime(true) - $this->proc->started, 3) . 's]';

        echo Terminal::getClearLine(); // Erase line width animation
        echo Terminal::decorate($err ? Level::DANGER : Level::INFO,
                $loadChar . ' ' . $message
            ) . $timeStr . ".\n";
    }

    /**
     * @param bool $showStderr
     * @return ProcBackground
     */
    public function setShowStderr(bool $showStderr): ProcBackground
    {
        $this->display[self::STDERR] = $showStderr;
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

    private function wrapPartialContent(int $fd): string
    {
        $pos                = strrpos($this->buffers[$fd], "\n");
        $out                = substr($this->buffers[$fd], 0, $pos + 1);
        $this->buffers[$fd] = substr($this->buffers[$fd], $pos + 1);
        return $out;
    }

    private function getLoaderChar(): string
    {
        static $counter;
        if (!isset($counter)) $counter = $this->proc->started;
        $pos = round($this->getElapsed() * 10) % $this->len;
        return mb_substr($this->str, $pos, 1);
    }

    /**
     * @return float
     */
    private function getElapsed(): float
    {
        return microtime(true) - $this->proc->started;
    }
}
