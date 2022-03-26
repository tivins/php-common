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
        return array_combine(array_keys($data), array_column($data, $lang));
    }

    /**
     * @return string[][]
     */
    abstract protected function getAll(): array;
}