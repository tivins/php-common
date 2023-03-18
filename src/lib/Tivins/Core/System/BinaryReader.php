<?php

namespace Tivins\Core\System;

use Exception;

class BinaryReader
{
    /**
     * @throws Exception
     */
    public static function open(string $filename): static
    {
        $fileHandle = fopen($filename, 'r');
        if (!$fileHandle) {
            throw new Exception('Failed to open file for reading');
        }
        return new static($fileHandle);
    }

    /**
     * @throws Exception
     */
    public function __construct(private $fileHandler)
    {
        if (!is_resource($this->fileHandler)) {
            throw new Exception('fileHandler is not a resource');
        }
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

    /**
     * @throws Exception
     */
    public function readChars(int $length): string
    {
        $data = fread($this->fileHandler, $length);
        if ($data === false) {
            throw new Exception();
        }
        return $data;
    }

    /**
     * @throws Exception
     */
    public function readSigned(int $bits): int|false
    {
        $bytes = $this->readChars($bits / 8);
        return match($bits) {
            8 => unpack('c', $bytes)[1],
            16 => unpack('s', $bytes)[1],
            32 => unpack('l', $bytes)[1],
            default => false
        };
    }

    /**
     * s : signed short (always 16 bit, machine byte order)
     * @return int
     * @throws Exception
     */
    public function readSignedShort(): int
    {
        $bytes = $this->readChars(2);
        return unpack('s', $bytes)[1];
    }

    /**
     * @throws Exception
     */
    public function readSignedInt32(): int
    {
        $bytes = $this->readChars(4);
        return unpack('l', $bytes)[1];
    }

    /**
     * v unsigned short (always 16 bit, little endian byte order)
     * @throws Exception
     */
    public function readU16LE(): int
    {
        $bytes = $this->readChars(2);
        return unpack('v', $bytes)[1];
    }

    /**
     * V unsigned long (always 32 bit, little endian byte order)
     * @throws Exception
     */
    public function readU32LE(): int
    {
        $bytes = $this->readChars(4);
        return unpack('V', $bytes)[1];
    }

    /**
     * P unsigned long long (always 64 bit, little endian byte order)
     * @throws Exception
     */
    public function readU64LE(): int
    {
        $bytes = $this->readChars(8);
        return unpack('P', $bytes)[1];
    }
}