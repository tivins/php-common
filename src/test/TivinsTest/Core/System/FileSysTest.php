<?php

namespace TivinsTest\Core\System;

use PHPUnit\Framework\TestCase;
use Tivins\Core\System\File;
use Tivins\Core\System\FileSys;

class FileSysTest extends TestCase
{
    public function testMkdirFile()
    {
        $file = '/tmp/path/to/a/file';
        self::assertTrue(FileSys::mkdirFile($file));
        self::assertDirectoryExists(dirname($file));

        $file = '/tmp/path/to/b/file';
        self::assertTrue(File::save($file, 'test'));
        self::assertDirectoryExists(dirname($file));
    }

    public function testLoad()
    {
        self::assertFalse(File::load('/anywhere/' . time()));
        $tmp = '/tmp/tmp-'.time().'.test';
        File::save($tmp, time());
        self::assertIsString(File::load($tmp));
    }

    public function testGetFileExtension()
    {
        self::assertEquals('jpg', File::getExtension('image.test.JPG'));
        self::assertEquals('jpg', File::getExtension ('image.png.test.jpg'));
        self::assertEquals('jpg', File::getExtension ('https://example.com/test.jpg'));
        self::assertEquals('', File::getExtension('imageJpG'));
    }

    public function testDelete()
    {
        $file = '/tmp/path/to/delete/file';
        self::assertFalse(File::isReadable($file));
        self::assertTrue(File::delete($file));
        self::assertTrue(File::save($file, 'test'));
        self::assertTrue(File::delete($file));
    }

    public function testJSON()
    {
        $file = '/tmp/' . sha1(microtime(true)) . '.json';
        $data = (object)['foo' => 'bar'];
        self::assertTrue(File::saveJSON($file, $data));
        self::assertEquals($data, File::loadJSON($file));
    }
}