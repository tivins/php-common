<?php

namespace Tivins\I18n;

abstract class Language
{
    public function ucWordsTitle(string $source): string
    {
        $linkingWords = $this->getLinkingWords($source);
        $words = preg_replace_callback('\w',
            function(array $matches) use($linkingWords): string {
                return mb_strtoupper($matches[0]);
            },
            $source);
        var_dump($words);
        return "bla bla bla...";
    }

    /**
     * @return string[] Ex : 'le','la','les','du','au', ...
     */
    abstract public function getLinkingWords(): array;
}