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
    public function get(Language $lang, Language $fallbackLang = Language::English): array
    {
        $data = $this->getAll();
        $combined = [];
        foreach ($data as $key => $values) {
            $combined[$key] = $values[$lang->value] ?? $values[$fallbackLang->value] ?? '';
        }
        return $combined;
    }

    public function createI18n(Language $lang, Language $fallbackLang): I18n
    {
        return new I18n($this->get($lang, $fallbackLang));
    }

    /**
     * @return string[][]
     */
    abstract protected function getAll(): array;
}