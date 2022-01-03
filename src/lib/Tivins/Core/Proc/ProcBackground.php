<?php

namespace Tivins\Core\Proc;

use Tivins\Core\System\Terminal;

class ProcBackground extends ProcHooks
{
    private string  $str      = '⠉⠛⠿⣿⣶⣤⣀⣤⣶⣿⠿⠛';
    private int     $full     = 3;
    private int     $idx      = 0;
    private int     $len;
    private int     $start;
    private int     $lastSize = 0;
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
    }

    public function onUpdate()
    {
        $loadChar = mb_substr($this->str, $this->idx, 1);
        $timeStr  = ' [' . number_format(microtime(true) - $this->start, 1) . 's] ';
        echo "\r" . Terminal::decorateSuccess($loadChar . $timeStr) . $this->message . '…';
        $this->lastSize = mb_strlen($loadChar . $timeStr . $this->message . '…');
        if ($this->idx++ == $this->len - 1) $this->idx = 0;
        usleep(200000);
    }

    public function onFinished()
    {
        $loadChar = mb_substr($this->str, $this->full, 1);
        $timeStr  = ' in [' . number_format(microtime(true) - $this->start, 3) . 's]';
        echo "\r" . str_repeat(' ', $this->lastSize);
        echo "\r" . Terminal::decorateInfo($loadChar . ' ' . ($this->endMessage ?: $this->message)) . $timeStr . ".\n";
    }
}