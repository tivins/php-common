<?php

namespace Tivins\Core\Routing;

class Route
{
    public function __construct(
       // public string $path,
        public string $class
    )
    {
    }

    public function trigger($args) {
        $instance = new $this->class;
        if ($instance instanceof Controller) {
            return $instance->query($args);
        }
        return null;
    }
}