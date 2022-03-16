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
}