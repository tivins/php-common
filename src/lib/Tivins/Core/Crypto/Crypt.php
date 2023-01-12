<?php

namespace Tivins\Core\Crypto;

use Tivins\Core\API\WebToken;
use Tivins\Core\System\File;

/**
 * @see WebToken
 */
class Crypt
{
    const Method = 'AES-256-CBC';
    private readonly string $pkey;

    public function __construct(string $file)
    {
        if (!file_exists($file)) {
            $res = openssl_pkey_new();
            openssl_pkey_export($res, $pkey);
            $this->pkey = $pkey;
            File::save($file, $this->pkey);
        } else {
            $this->pkey = File::load($file);
        }
    }

    public function encrypt($plaintext): string
    {
        $key        = hash('sha256', $this->pkey, true);
        $iv         = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, self::Method, $key, OPENSSL_RAW_DATA, $iv);
        $hash       = hash_hmac('sha256', $ciphertext . $iv, $key, true);
        return base64_encode($iv . $hash . $ciphertext);
    }

    public function decrypt($ivHashCiphertext): string|false
    {
        $ivHashCiphertext = base64_decode($ivHashCiphertext);
        $iv         = substr($ivHashCiphertext, 0, 16);
        $hash       = substr($ivHashCiphertext, 16, 32);
        $ciphertext = substr($ivHashCiphertext, 48);
        $key        = hash('sha256', $this->pkey, true);

        if (!hash_equals(hash_hmac('sha256', $ciphertext . $iv, $key, true), $hash)) {
            return false;
        }

        return openssl_decrypt($ciphertext, self::Method, $key, OPENSSL_RAW_DATA, $iv);
    }
}
