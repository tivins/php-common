<?php

namespace Tivins\Core\Code;

use JsonSerializable;

class JSONable implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}