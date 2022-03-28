<?php

namespace Tivins\Core\HTML;


use Tivins\Core\Code\Exception;

class FormSecurity
{
    /**
     * Generates cryptographic tokens for the session.
     *
     * @throws Exception
     */
    public static function init()
    {
        try {

        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        if (empty($_SESSION['token2'])) {
            $_SESSION['token2'] = random_bytes(32);
        }
        }
        catch (\Exception $ex)
        {
            throw new Exception(
                'Cannot initialize the form tokens',
                'Cannot find an appropriate source of randomness cannot be found. ' . $ex->getMessage()
            );
        }
    }

    /**
     * Get a base64 encoded token to be used in forms.
     * @throws Exception if the session is not initialized.
     */
    public static function getPublicToken(string $formId): string
    {
        self::throwException();
        return base64_encode(
            join('.', [
                $formId,
                $_SESSION['token'],
                self::getToken($formId)
            ])
        );
    }

    /**
     * @throws Exception if the session is not initialized.
     */
    private static function throwException()
    {
        if (!empty($_SESSION['token']) && !empty($_SESSION['token2'])) {
            return;
        }
        throw new Exception(
            'Invalid form configuration',
            'FormSecurity: The session tokens was not generated. See FormSecurity::init().'
        );
    }

    /**
     * @throws Exception if the session is not initialized.
     */
    private static function getToken(string $formId): string
    {
        self::throwException();
        return hash_hmac('sha256', $formId, $_SESSION['token2']);
    }

    /**
     * Checks if the given token match with the generated token based on the form id.
     * @throws Exception if the session is not initialized.
     */
    public static function checkPostedToken(string $formId, string $postedToken): bool
    {
        self::throwException();
        $decoded = array_filter(explode('.', base64_decode($postedToken)));
        return count($decoded) == 3 // we need our 3 values
            && $decoded[0] === $formId // check the form id
            && hash_equals($_SESSION['token'], $decoded[1]) // check the main token
            && hash_equals(self::getToken($formId), $decoded[2]) // check the form token
            ;
    }
}
