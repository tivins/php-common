<?php

namespace Tivins\Core\HTML;

use DOMDocument;
use DOMXPath;

class XML
{
    public static function getNodeAttr(string $html, string $selector, string $attribute): string
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        libxml_use_internal_errors(false);

        $xpath = new DomXpath($doc);
        foreach ($xpath->query($selector) as $link) {
            return $link->getAttribute($attribute);
        }
        return '';
    }
}