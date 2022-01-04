<?php

namespace Tivins\Core\Proc;

use Tivins\Core\Log\Level;
use Tivins\Core\System\Terminal;

class ProcBackground extends Process
{
    private string  $str      = '⠉⠛⠿⣿⣶⣤⣀⣤⣶⣿⠿⠛';
    private int     $len;
    private int     $start;
    private string  $message;
    private ?string $endMessage;

    private array $buffers = [self::STDOUT=>'',self::STDERR=>''];

    public function __construct(string $message, ?string $endMessage = null)
    {
        $this->message    = $message;
        $this->endMessage = $endMessage;
        $this->len        = mb_strlen($this->str);
        $this->start      = microtime(true);
    }

    public function onStart()
    {
        // stream_set_blocking($this->proc->pipes[1], false);
        // stream_set_blocking($this->proc->pipes[2], false);
    }

    public function wrapPartialContent(string &$str): string
    {
        $pos = strrpos($str, "\n");
        $out = substr($str, 0, $pos + 1);
        $str = substr($str, $pos + 1);
        return $out;
    }

    private function getLoaderChar(): string
    {
        static $counter;
        if (!isset($counter)) $counter = 0;
        if ($counter++ == $this->len - 1) $counter = 0;
        return mb_substr($this->str, $counter, 1);
    }

    public function onUpdate(array $status, array $received)
    {
        $this->buffers[self::STDOUT] .= $received[self::STDOUT];
        $this->buffers[self::STDERR] .= $received[self::STDERR];

        $stdout = $this->wrapPartialContent($this->proc->stdout);
        $stderr = $this->wrapPartialContent($this->proc->stderr);

        echo Terminal::getClearLine();
        echo Terminal::decorateInfo($stdout);
        echo Terminal::decorateDanger($stderr);

        $loader = $this->getLoaderChar()
            . ' [' . number_format(microtime(true) - $this->start, 1) . 's] '
            ;

        echo "\r" . Terminal::decorateSuccess($loader) . $this->message . "…";

        // $nb = 1;
        // if ($stdout) { echo Terminal::decorate(Level::DEBUG, "  ⏵ " . $stdout) . "\n"; $nb++; }
        // if ($stderr) { echo Terminal::decorate(Level::DANGER, "  ⏵ " . $stderr) . "\n"; $nb++; }
        // echo "\033[{$nb}F\033[" . Terminal::getWidth() . "C";

        usleep(200000);
    }

    public function onFinish()
    {
        echo Terminal::getClearLine();

        $err = $this->proc->hasError();
        $loadChar = $err ? 'x' : '✓'; // mb_substr($this->str, $this->full, 1);
        $timeStr  = ' in [' . number_format(microtime(true) - $this->start, 3) . 's]';

        echo Terminal::getClearLine(); // Erase line width animation
        echo Terminal::decorate($err ? Level::DANGER : Level::INFO,
                $loadChar . ' ' . ($this->endMessage ?: $this->message)) . $timeStr . ".\n";
    }
}
