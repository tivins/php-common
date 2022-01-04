<?php

namespace Tivins\Core\Proc;

use Tivins\Core\Log\Level;
use Tivins\Core\System\Terminal;

class ProcBackground extends ProcHooks
{
    private string  $str      = '⠉⠛⠿⣿⣶⣤⣀⣤⣶⣿⠿⠛';
    private int     $full     = 3;
    private int     $idx      = 0;
    private int     $len;
    private int     $start;
    private string  $message;
    private ?string $endMessage;

    public function __construct(string $message, ?string $endMessage = null)
    {
        $this->message    = $message;
        $this->endMessage = $endMessage;
        $this->len        = mb_strlen($this->str);
        $this->start      = microtime(true);
    }

    public function onStart()
    {
        stream_set_blocking($this->proc->pipes[1], false);
        stream_set_blocking($this->proc->pipes[2], false);
    }

    public function getLastLine(string $str): string
    {
        $stdout = explode("\n", trim($str));
        return end($stdout);
    }

    public function onUpdate()
    {
        $this->proc->stdout .= stream_get_contents($this->proc->pipes[1]);
        $this->proc->stderr .= stream_get_contents($this->proc->pipes[2]);
        $stdout = $this->getLastLine($this->proc->stdout);
        $stderr = $this->getLastLine($this->proc->stderr);

        $loadChar = mb_substr($this->str, $this->idx, 1);
        $timeStr  = ' [' . number_format(microtime(true) - $this->start, 1) . 's] ';

        echo "\r" . Terminal::decorateSuccess($loadChar . $timeStr) . $this->message . "…\n";

        $nb = 1;
        if ($stdout) { echo Terminal::decorate(Level::DEBUG, "  ⏵ " . $stdout) . "\n"; $nb++; }
        if ($stderr) { echo Terminal::decorate(Level::DANGER, "  ⏵ " . $stderr) . "\n"; $nb++; }
        echo "\033[{$nb}F\033[" . Terminal::getWidth() . "C";

        if ($this->idx++ == $this->len - 1) $this->idx = 0;
        usleep(200000);
    }

    public function onFinished()
    {
        $stdout = $this->getLastLine($this->proc->stdout);
        $stderr = $this->getLastLine($this->proc->stderr);

        $loadChar = $stderr ? 'x' : '✓'; // mb_substr($this->str, $this->full, 1);
        $timeStr  = ' in [' . number_format(microtime(true) - $this->start, 3) . 's]';

        echo Terminal::eraseCurrentLine(); // Erase line width animation
        echo Terminal::decorate($stderr ? Level::DANGER : Level::INFO, $loadChar . ' ' . ($this->endMessage ?: $this->message)) . $timeStr . ".\n";

        $nb = 0;
        if ($stdout) { $nb++; echo Terminal::eraseCurrentLine()."\n";} // Erase line of stdout
        if ($stderr) { $nb++; echo Terminal::eraseCurrentLine()."\n";} // Erase line of stderr
        echo "\033[{$nb}F";
        if ($stderr) { echo Terminal::decorateDanger("  ⏵ " . $stderr)."\n";} // Erase line of stderr
    }
}
