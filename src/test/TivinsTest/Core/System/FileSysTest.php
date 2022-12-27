<?php

namespace TivinsTest\Core\System;

use PHPUnit\Framework\TestCase;
use Tivins\Core\System\FileSys;

class FileSysTest extends TestCase
{
    public function testMkdirFile()
    {
        $file = '/tmp/path/to/a/file';
        self::assertTrue(FileSys::mkdirFile($file));
        self::assertDirectoryExists(dirname($file));

        $file = '/tmp/path/to/b/file';
        self::assertTrue(FileSys::writeFile($file, 'test'));
        self::assertDirectoryExists(dirname($file));
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
        self::assertEquals('jpg', FileSys::getFileExtension ('https://example.com/test.jpg'));
        self::assertEquals('', FileSys::getFileExtension('imageJpG'));
    }

    public function testDelete()
    {
        $file = '/tmp/path/to/delete/file';
        self::assertFalse(FileSys::isReadable($file));
        self::assertTrue(FileSys::delete($file));
        self::assertTrue(FileSys::writeFile($file, 'test'));
        self::assertTrue(FileSys::delete($file));
    }

    public function testJSON()
    {
        $file = '/tmp/' . sha1(microtime(true)) . '.json';
        $data = (object) ['foo' => 'bar'];
        self::assertTrue(FileSys::writeJSONFile($file, $data));
        self::assertEquals($data, FileSys::loadJSONFile($file));
    }
}