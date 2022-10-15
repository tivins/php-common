<?php

namespace Tivins\Core\Routing;

class Router
{
    /**
     * @var string[]
     */
    private array $pathes = [];
    private string $last_match = '';

    /**
     *
     */
    public function register(string $path, string $ctrlClass): void
    {
        $this->pathes[$path] = $ctrlClass;
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
    private function transformMatch($class, $args): ?\Tivins\Core\Http\Response
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
    public function find(string $path): ?\Tivins\Core\Http\Response
    {
        // fast, complete match
        if (isset($this->pathes[$path])) {
            $this->last_match = $path;
            return $this->transformMatch($this->pathes[$path], []);
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
                return $this->transformMatch($data, $m);
            }
        };
        // no match
        return null;
    }

    public function getLastMatch() : string { return $this->last_match; }
}