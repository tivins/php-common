<?php

namespace Tivins\Core;

/**
 * A `getopts()` wrapper.
 *
 * ```php
 * $opts = OptionsArgs::newParsed(
 *      new OptionArg('uri', true, 'u'),
 *      new OptionArg('user-id', true),
 *      new OptionArg('notify-id', true),
 *      new OptionArg('verbose', false, 'v'),
 * );
 * $uri = $opts->getValue('uri');
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

    private array $parsed = [];

    public function __construct(OptionArg ...$args) {
        $this->add(...$args);
    }

    /**
     * Add one or more options to parse.
     *
     * @param OptionArg ...$args
     * @return $this
     *
     */
    public function add(OptionArg ...$args): static
    {
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
     */
    public function parse(array|null $data = null): array
    {
        $short = '';
        $longs = [];
        foreach ($this->args as $arg) {
            if ($arg->short) {
                $short .= $arg->short . ($arg->requireValue ? ':' : '');
            }
            if ($arg->long) {
                $longs[] = $arg->long . ($arg->requireValue ? ':' : '');
            }
        }

        $opts = is_null($data) ? getopt($short, $longs) : $data;

        foreach ($this->args as $arg)
        {
            // copy value to long and remove short.
            if (isset($opts[$arg->short])) {
                $opts[$arg->long] = $opts[$arg->short];
                unset($opts[$arg->short]);
            }
        }

        $this->parsed = $opts ?: [];
        return $this->parsed;
    }

    public function getParsed(): array
    {
        return $this->parsed;
    }

    public function getValue(string $long): string|false {
        return $this->parsed[$long] ?? false;
    }

    public static function newParsed(OptionArg ...$args): static {
        $inst = new static(...$args);
        $inst->parse();
        return $inst;
    }
}
