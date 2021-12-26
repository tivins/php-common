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
        $this->args = array_merge($this->args, $args);
        return $this;
    }

    /**
     * Get the parsed options.
     *
     * @return array|bool
     */
    public function parse(): array|bool
    {
        $short = '';
        $longs = [];
        foreach ($this->args as $arg)
        {
            $short .= $arg->getShort();
            if ($arg->requireValue()) {
                $short .= ':';
            }
            if ($arg->getLong()) {
                $longs[] = $arg->getLong();
            }
        }
        $opts = getopt($short, $longs);

        foreach ($this->args as $arg) {
            if (!$arg->getLong()) {
                continue;
            }
            if (isset($opts[$arg->getLong()])) {
                $opts[$arg->getShort()] = $opts[$arg->getLong()];
            }
            elseif (isset($opts[$arg->getShort()])) {
                $opts[$arg->getLong()] = $opts[$arg->getShort()];
            }
        }

        return $opts;
    }
}
