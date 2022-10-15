<?php

namespace Tivins\Core\Routing;

use Tivins\Core\Http\Response;

class Router
{
    /**
     * @var string[]
     */
    private array  $paths      = [];
    private string $last_match = '';

    /**
     * @see registerPaths
     */
    public function register(string $path, string $ctrlClass): void
    {
        $this->paths[$path] = $ctrlClass;
    }

    /**
     * @see register
     */
    public function registerPaths(array $data): void
    {
        $this->paths = array_merge($this->paths, $data);
    }

    /**
     *
     */
    private function transformMatch($class, $args): ?Response
    {
        $instance = new $class;
        if ($instance instanceof Controller) {
            return $instance->query($args);
        }
        return null;
    }
    /**
     *
     */
    public function find(string $path): ?Response
    {
        // fast, complete match
        if (isset($this->paths[$path])) {
            $this->last_match = $path;
            return $this->transformMatch($this->paths[$path], []);
        }
        // preg match
        foreach ($this->paths as $regexp => $data) {
            if (!str_contains($regexp, '(')) {
                continue;
            }
            $m = [];
            if (preg_match('~' . $regexp . '~', $path, $m)) {
                array_shift($m); // remove $m[0]
                $this->last_match = $regexp;
                return $this->transformMatch($data, $m);
            }
        };
        // no match
        return null;
    }

    public function getLastMatch() : string
    {
        return $this->last_match;
    }
}