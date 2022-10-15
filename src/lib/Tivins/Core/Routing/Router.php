<?php

namespace Tivins\Core\Routing;

class Router
{
    private array $pathes = [];
    private string $last_match = '';

    /**
     *
     */
    public function register(string $path, array $data): void
    {
        $this->pathes[$path] = $data;
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
    public function find(string $path): ?array
    {
        // fast, complete match
        if (isset($this->pathes[$path])) {
           $this->last_match = $path;
            return $this->pathes[$path] + ['args' => []];
        }
        // preg match
        foreach ($this->pathes as $regexp => $data) {
            if (strpos($regexp, '(') === false) {
                continue;
            }
            $m = [];
            if (preg_match('~' . $regexp . '~', $path, $m)) {
                array_shift($m); // remove $m[0]
                $this->last_match = $regexp;
                return $data + ['args' => $m];
            }
        };
        // no match
        return null;
    }

    public function getLastMatch() : string { return $this->last_match; }
}