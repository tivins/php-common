<?php

namespace Tivins\Core;

class Util
{
    public static function getObjectID(object $object): int
    {
        return spl_object_id($object);
    }
}