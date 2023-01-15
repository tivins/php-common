<?php

namespace Tivins\Baz\Install\Schema;


use Tivins\Core\API\APIAccess;

readonly class Enum
{
    public function __construct(
        public string $name = '',
        public string $type = 'int',
        public string $comment = '',
        public array $cases = [],
        public APIAccess|null $access = null,
    ) {
    }
}
