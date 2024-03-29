<?php

namespace Tivins\Core\Http;
use Tivins\Core\Net\Http\Status;
use Tivins\Core\Net\Http\ContentType;

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

    public static function sendResponse(Response $response): never
    {
        self::send($response->getBody(), $response->getContentType(), $response->getStatus());
    }

    /**
     * @param string $content The body
     * @param ContentType $type
     * @param Status $status HTTP Response status
     * @return never
     */
    public static function send(string $content, ContentType $type = ContentType::HTML, Status $status = Status::OK): never
    {
        http_response_code($status->value);
        header('Content-Type: ' . $type->value . '; charset=utf-8');
        echo $content;
        exit;
    }

    public static function sendJSON(mixed $content, Status $status = Status::OK): never
    {
        self::send(json_encode($content), ContentType::JSON, $status);
    }

}