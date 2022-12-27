<?php

namespace Tivins\Core\Code;

use ReflectionEnum;
use Tivins\Core\Intl\Intl;

trait EnumExtra
{
    public static function tryFromName(string $name): ?static
    {
        $reflection = new ReflectionEnum(static::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        return $reflection->hasCase($name) ? $reflection->getCase($name)->getValue() : null;
    }

    /**
     * @return array<string, string>
     */
    public static function getAssociative(): array
    {
        $cases = static::cases();
        return array_combine(
            array_column($cases, 'value'),
            array_map(fn(self $n) => $n->translate(), $cases)
        );
    }

    public function translate(): string
    {
        $intl_key = strtolower(str_replace('\\', '_', static::class)) . '_' . $this->name;
        return Intl::get($intl_key);
    }
}