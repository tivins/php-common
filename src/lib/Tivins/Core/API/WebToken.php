<?php

namespace Tivins\Core\API;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Tivins\Baz\Core\FSUtil;

/**
 * Abstract class to be extends and need to define following properties :
 * - $privateKeyPath [required] : The file path of the private key.
 * - $publicKeyPath [required] : The file path of the public key.
 * - $issuer [required] : The issues of the token (Application URL or server name)
 * - $audience [security] : The audience able to read tokens.
 *
 *
 * Generating a private and a public keys
 *
 *     openssl genrsa -out cert.pem 1024
 *     openssl rsa -in cert.pem  -pubout > cert.pub
 *
 * Avoid inclusion in repository.
 *
 *     echo 'cert.pem' >> .gitignore
 *     echo 'cert.pub' >> .gitignore
 *
 */
abstract class WebToken
{
    private const Algo = 'RS256';
    private const DurationSeconds = 600;
    private const TokenDataKey = 'data';

    protected string $privateKeyPath = '';
    protected string $publicKeyPath  = '';
    protected string $audience = '';
    protected string $issuer = '';

    /**
     * Encode the given data using private key.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc7519#section-4.1
     * @throws Exception
     */
    public function encode(mixed $data): string
    {
        $token = [
            'iss' => $this->issuer,
            'iat' => time(),
            'exp' => time() + self::DurationSeconds,
            self::TokenDataKey => $data,
            // 'nbf'
        ];
        if (!empty($this->audience)) {
            $token['aud'] = $this->audience;
        }

        return JWT::encode($token, $this->getPrivateKey(), self::Algo);
    }

    /**
     * Decode the given token in
     *
     * @throws Exception
     */
    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->getPublicKey(), self::Algo));
    }

    /**
     * @throws Exception
     */
    private function getPrivateKey(): string|false
    {
        if (! is_readable($this->privateKeyPath))
        {
            throw new Exception('private_key_missing');
        }
        return FSUtil::loadFile($this->privateKeyPath);
    }

    /**
     * @throws Exception
     */
    private function getPublicKey(): string|false
    {
        if (! is_readable($this->privateKeyPath))
        {
            throw new Exception('private_key_missing');
        }
        return FSUtil::loadFile($this->publicKeyPath);
    }
}