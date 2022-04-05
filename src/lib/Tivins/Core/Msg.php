<?php

namespace Tivins\Core;

use Exception;

class Msg
{
    public const Error   = 'error';
    public const Warning = 'warning';
    public const Success = 'success';

    public function __construct(private string $group = 'default')
    {
    }

    /**
     * Push a new message.
     */
    public function push(string $msg, string $type) : void
    {
        $_SESSION['msg'][$this->group][] = [$msg, $type];
    }

    public function pushException(Exception $exception)
    {
        $this->push($exception->getMessage(), self::Error);
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

