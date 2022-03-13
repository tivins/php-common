<?php

namespace Tivins\Core;

class Msg
{
    public const Error   = 'error';
    public const Warning = 'warning';
    public const Success = 'success';

    /**
     * Push a new message.
     */
    public function push(string $msg, string $type) : void
    {
        $_SESSION['msg'][] = [$msg, $type];
    }

    /**
     * Clear the queue and return messages as HTML.
     */
    public function get() : string
    {
        if (empty($_SESSION['msg'])) return '';
        $messages = $_SESSION['msg'];
        $_SESSION['msg'] = [];

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

