<?php

namespace Tivins\Core\System;

use Tivins\Core\Log\Level;

/*
 * https://rosettacode.org/wiki/Terminal_control/Cursor_positioning#PHP
 * echo "\033[".$x.",".$y."H"; // Position line $y and column $x.
 * echo "\033[".$n."A"; // Up $n lines.
 * echo "\033[".$n."B"; // Down $n lines.
 * echo "\033[".$n."C"; // Forward $n columns.
 * echo "\033[".$n."D"; // Backward $n columns.
 * echo "\033[2J"; // Clear the screen, move to (0,0).
 *
 * printf "\033[1A"  # move cursor one line up
 * printf "\033[K"   # delete till end of line
 *
 * tput cols
 * tput lines
 *
 * - Position the Cursor:
  \033[<L>;<C>H
     Or
  \033[<L>;<C>f
  puts the cursor at line L and column C.
- Move the cursor up N lines:
  \033[<N>A
- Move the cursor down N lines:
  \033[<N>B
- Move the cursor forward N columns:
  \033[<N>C
- Move the cursor backward N columns:
  \033[<N>D

- Clear the screen, move to (0,0):
  \033[2J
- Erase to end of line:
  \033[K

- Save cursor position:
  \033[s
- Restore cursor position:
  \033[u
 */

/**
 *
 */
class Terminal
{
    public static function decorateSuccess(string $str): string { return self::decorate(Level::SUCCESS, $str); }
    public static function decorateDanger(string $str): string { return self::decorate(Level::DANGER, $str); }
    public static function decorateInfo(string $str): string { return self::decorate(Level::INFO, $str); }

    public static function decorate(Level $level, string $str): string
    {
        return match ($level) {
            Level::DANGER   => "\033[31m$str\033[0m",
            Level::SUCCESS  => "\033[32m$str\033[0m",
            Level::WARNING  => "\033[33m$str\033[0m",
            Level::INFO     => "\033[36m$str\033[0m",
            Level::DEBUG    => "\033[37m$str\033[0m",
            default         => $str,
        };
    }

    public static function getWidth(): int {
        return shell_exec('tput cols');
    }

    public static function eraseCurrentLine()
    {
        return "\r\033[K";
    }
}