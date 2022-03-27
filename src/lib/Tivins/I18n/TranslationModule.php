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
        $combined = [];
        foreach ($data as $key => $values) {
            $combined[$key] = $values[$lang] ?? $values['en'] ?? '';
        }
        return $combined;
    }

    /**
     * @return string[][]
     */
    abstract protected function getAll(): array;
}