<?php

namespace Tivins\Core\Code;

use Tivins\Core\Util;

trait FastToString
{
    /**
     * @return string Something like `FullQualified\Namespace\Class#126`
     */
    public function __toString(): string
    {
        return static::class . '#' . Util::getObjectID($this);
    }
}