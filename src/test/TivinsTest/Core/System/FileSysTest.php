<?php

namespace TivinsTest\Core\System;

use PHPUnit\Framework\TestCase;
use Tivins\Core\System\FileSys;

class FileSysTest extends TestCase
{
    public function testMkdirFile()
    {
        $file = '/tmp/path/to/a/file';
        $this->assertTrue(FileSys::mkdirFile($file));
        $this->assertDirectoryExists(dirname($file));
    }
}