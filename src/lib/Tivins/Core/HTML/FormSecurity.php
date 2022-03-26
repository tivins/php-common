<?php

namespace Tivins\Core\HTML;

use Exception;

class FormSecurity
{
    /**
     * @throws Exception
     */
    public static function init()
    {
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        if (empty($_SESSION['token2'])) {
            $_SESSION['token2'] = random_bytes(32);
        }
    }

    /**
     *
     */
    public static function getPublicToken(string $formId): string
    {
        return base64_encode(
            join('.', [
                $formId,
                $_SESSION['token'],
                self::getToken($formId)
            ])
        );
    }

    /**
     *
     */
    public static function checkPostedToken(string $formId, string $postedToken): bool
    {
        $decoded = array_filter(explode('.', base64_decode($postedToken)));
        return count($decoded) == 3         // we need our 3 values
            && $decoded[0] === $formId      // check the form id
            && hash_equals($_SESSION['token'], $decoded[1])  // check the main token
            && hash_equals(self::getToken($formId), $decoded[2]) // check the form token
            ;
    }

    /**
     *
     */
    private static function getToken(string $formId): string
    {
        return hash_hmac('sha256', $formId, $_SESSION['token2']);
    }
}
