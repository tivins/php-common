<?php

namespace Tivins\Core\Routing;

class Router
{
    /**
     * @var Route[]
     */
    private array $pathes = [];
    private string $last_match = '';

    /**
     *
     */
    public function register(string $path, Route $ctrl): void
    {
        $this->pathes[$path] = $ctrl;
    }

    /**
     *
     */
    public function registerPaths(array $data): void
    {
        $this->pathes = array_merge($this->pathes, $data);
    }

    /**
     *
     */
    private function transformMatch($class, $args)
    {
        $obj = new $class;
        $obj->trigger();
    }
    /**
     *
     */
    public function find(string $path): ?array
    {
        // fast, complete match
        if (isset($this->pathes[$path])) {
           $this->last_match = $path;
            return $this->pathes[$path]->trigger([]);
        }
        // preg match
        foreach ($this->pathes as $regexp => $data) {
            if (!str_contains($regexp, '(')) {
                continue;
            }
            $m = [];
            if (preg_match('~' . $regexp . '~', $path, $m)) {
                array_shift($m); // remove $m[0]
                $this->last_match = $regexp;
                return $data->trigger($m);
            }
        };
        // no match
        return null;
    }

    public function getLastMatch() : string { return $this->last_match; }
}