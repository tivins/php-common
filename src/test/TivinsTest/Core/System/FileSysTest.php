<?php

namespace TivinsTest\Core\System;

use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\File;
use Tivins\Core\System\FileSys;

class FileSysTest extends TestCase
{
    public function testMkdirFile()
    {
        $file = '/tmp/path/to/a/file';
        $this->assertTrue(FileSys::mkdirFile($file));
        $this->assertDirectoryExists(dirname($file));

        $file = '/tmp/path/to/b/file';
        $this->assertTrue(FileSys::writeFile($file, 'test'));
        $this->assertDirectoryExists(dirname($file));
    }

    public function testLoad()
    {
        self::assertFalse(FileSys::loadFile('/anywhere/' . time()));
        $tmp = '/tmp/tmp-'.time().'.test';
        FileSys::writeFile($tmp, time());
        self::assertIsString(FileSys::loadFile($tmp));
    }

    public function testGetFileExtension()
    {
        self::assertEquals('jpg', FileSys::getFileExtension('image.test.JPG'));
        self::assertEquals('jpg', FileSys::getFileExtension ('image.test..JPG'));
        self::assertEquals('', FileSys::getFileExtension('imageJpG'));
    }

    public function testDelete()
    {
        $file = '/tmp/path/to/delete/file';
        $this->assertFalse(FileSys::isReadable($file));
        $this->assertTrue(FileSys::delete($file));
        $this->assertTrue(FileSys::writeFile($file, 'test'));
        $this->assertTrue(FileSys::delete($file));
    }
}