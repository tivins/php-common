<?php

namespace TivinsTest\Core\Crypto;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Crypto\Crypt;
use Tivins\Core\System\File;
use Tivins\Core\System\FileSys;
use function PHPUnit\Framework\assertEquals;

class CryptTest extends TestCase
{
    public function testMain()
    {
        $toCrypt = 'This is a test string.';
        $file = '/tmp/tmp1_'.sha1(microtime(true));
        $file2 = '/tmp/tmp2_'.sha1(microtime(true));
        self::assertFileDoesNotExist($file);
        $crypt = new Crypt($file);
        self::assertFileExists($file);
        $encrypted = $crypt->encrypt($toCrypt);
        self::assertNotEquals($encrypted, $toCrypt);
        $crypt2 = new Crypt($file); // Use a new crypto to be sure that nothing remains from previous objects.
        $decrypted = $crypt2->decrypt($encrypted);
        self::assertEquals($toCrypt, $decrypted);
        $crypt3 = new Crypt($file2); // Use a new crypto to be sure that nothing remains from previous objects.
        $decryptedFail = $crypt3->decrypt($encrypted);
        self::assertNotEquals($toCrypt, $decryptedFail);
        File::delete($file);
        self::assertFileDoesNotExist($file);
    }
}
