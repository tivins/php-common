<?php

namespace Tivins\I18n;
/**
 * Usage:
 * ```
 * class MyTranslation extends TranslationModule
 * {
 *     public function getAll(): array {
 *          return [
 *              'key1' => ['en' => 'Value', 'fr' => 'Valeur'],
 *              // ...
 *          ];
 *     }
 * }
 * ```
 */
abstract class TranslationModule
{
    public function get(string $lang): array
    {
        $data = $this->getAll();
        $default = array_column($data, 'en');
        return array_combine(array_keys($data), array_column($data, $lang) + $default);
    }

    /**
     * @return string[][]
     */
    abstract protected function getAll(): array;
}