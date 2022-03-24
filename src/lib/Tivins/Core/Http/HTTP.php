<?php

namespace Tivins\Core\Http;

/**
 * Perform actions on HTTP content.
 */
class HTTP
{
    /**
     * Send the Location HTTP Header.
     *
     * @param string $url
     * @return never
     */
    public static function redirect(string $url = '/'): never
    {
        header('Location: ' . $url);
        exit(0);
    }

    /**
     * @param string $content The body
     * @param string $type Content Type (eg: application/json, ...)
     * @param Status $status HTTP Response status
     * @return never
     */
    public static function send(string $content, string $type = 'text/html', Status $status = Status::OK): never
    {
        http_response_code($status->value);
        header('Content-Type: ' . $type . '; charset=utf-8');
        echo $content;
        exit;
    }

    public static function sendJSON(mixed $content, Status $status = Status::OK): never
    {
        self::send(json_encode($content), 'application/json', $status);
    }

}