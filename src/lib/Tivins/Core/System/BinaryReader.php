<?php

namespace Tivins\Core\System;

use RuntimeException;

class BinaryReader
{
    public static function open(string $filename): static
    {
        $fileHandle = fopen($filename, 'r');
        if (!$fileHandle) {
            throw new RuntimeException('Failed to open file for reading');
        }
        return new static($fileHandle);
    }

    public function __construct(private $fileHandler)
    {
    }

    public function close(): bool
    {
        return fclose($this->fileHandler);
    }

    public function tell(): int|false
    {
        return ftell($this->fileHandler);
    }

    public function seek(int $offset, int $whence = SEEK_SET): int
    {
        return fseek($this->fileHandler, $offset, $whence);
    }

    public function readChars(int $length): string|false
    {
        return fread($this->fileHandler, $length);
    }

    // v unsigned short (always 16 bit, little endian byte order)
    public function readU16LE(): int
    {
        $bytes = $this->readChars(2);
        return unpack('v', $bytes)[1];
    }

    // V unsigned long (always 32 bit, little endian byte order)
    public function readU32LE(): int
    {
        $bytes = $this->readChars(4);
        return unpack('V', $bytes)[1];
    }

    // P unsigned long long (always 64 bit, little endian byte order)
    public function readU64LE(): int
    {
        $bytes = $this->readChars(8);
        return unpack('P', $bytes)[1];
    }
}