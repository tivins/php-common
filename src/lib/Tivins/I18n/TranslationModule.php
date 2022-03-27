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

    public function createI18n(string $lang): I18n
    {
        return new I18n($this->get($lang));
    }

    /**
     * @return string[][]
     */
    abstract protected function getAll(): array;
}