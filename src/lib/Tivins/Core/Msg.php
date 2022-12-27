<?php

namespace Tivins\Core;

use Psr\Log\LogLevel;
use Throwable;
use Tivins\Core\Log\Level;

class Msg
{
    public const Error   = 'error';
    public const Warning = 'warning';
    public const Success = 'success';

    public function __construct(private readonly string $group = 'default')
    {
    }

    /**
     * Push a new message.
     */
    public function push(string $msg, Level $type) : void
    {
        $_SESSION['msg'][$this->group][] = [$msg, $type];
    }

    public function pushException(Throwable $exception): void
    {
        $this->push($exception->getMessage(), Level::DANGER);
    }

    /**
     * Clear the queue and return messages as HTML.
     */
    public function get() : string
    {
        if (empty($_SESSION['msg'][$this->group])) return '';
        $messages = $_SESSION['msg'][$this->group];
        $_SESSION['msg'][$this->group] = [];

        return $this->wrap( // customize wrapper
            implode( // convert String[] to String
                array_map([$this, 'render'], $messages) // customize msg
            )
        );
    }

    /**
     * Default theme for the wrapper
     */
    protected function wrap(string $html) : string
    {
        return '<div class="alerts">' . $html . '</div>';
    }

    /**
     * Default theme for a message
     */
    protected function render(array $msgData) : string
    {
        return '<div class="alert ' . $msgData[1] . '">' . $msgData[0] . '</div>';
    }
}

