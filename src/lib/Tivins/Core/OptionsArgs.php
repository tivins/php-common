<?php

namespace Tivins\Core;

/**
 * A `getopts()` wrapper.
 *
 * ```php
 * $opts = (new OptionsArgs())
 *     ->add(new OptionArg('h', false, 'help'))
 *     ->add(new OptionArg('f', true, 'filename'))
 *     ->parse();
 * var_dump($opts);
 * ```
 * ```sh
 * program.php -h
 * program.php -hf path/to/file
 * program.php --filename path/to/file
 * ```
 *
 */
class OptionsArgs
{
    /**
     * Storage for given options.
     *
     * @var OptionArg[]
     */
    private array $args = [];

    /**
     * Add one or more options to parse.
     *
     * @param OptionArg ...$args
     * @return $this
     *
     */
    public function add(OptionArg ...$args): self
    {
        // $this->args = array_merge($this->args, $args);
        foreach ($args as $arg) {
            $this->args[$arg->getId()] = $arg;
        }
        return $this;
    }

    /**
     * Get the parsed options.
     *
     * @param array|null $data The data source to parse.
     *      If $data is null, `getopt()` will be used.
     *      If $data is an array, this will be the source of input.
     *
     * @return array
     */
    public function parse(array|null $data = null): array
    {
        $short = '';
        $longs = [];
        foreach ($this->args as $arg) {
            $short .= $arg->getShort() . ($arg->requireValue() ? ':' : '');
            if ($arg->getLong()) {
                $longs[] = $arg->getLong() . ($arg->requireValue() ? ':' : '');
            }
        }

        $opts = is_null($data) ? getopt($short, $longs) : $data;

        foreach ($this->args as $arg)
        {
            if (!$arg->getLong()) {
                continue;
            }
            // copy value to long and remove short.
            if (isset($opts[$arg->getShort()])) {
                $opts[$arg->getLong()] = $opts[$arg->getShort()];
                unset($opts[$arg->getShort()]);
            }
        }

        return $opts ?: [];
    }
    /*
    private function getOpts(string $shortOrLong): ?OptionArg {
        foreach ($this->args as $id => $arg) {
            if (in_array($shortOrLong, [$arg->getShort(),$arg->getLong()])) {
                return $arg;
            }
        }
        return null;
    }
    */
}
