<?php

namespace Tivins\Core\HTML;

use DOMDocument;
use DOMNode;
use DOMXPath;

class DOM
{
    public static function getDoc(string $html): DOMDocument
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        libxml_use_internal_errors(false);
        return $doc;
    }

    public static function getNode(string $html, string $selector): ?DOMNode
    {
        $xpath = new DomXpath(self::getDoc($html));
        $list = $xpath->query($selector);
        if ($list->length) {
            return $list->item(0);
        }
        return null;
    }

    public static function getNodeAttr(string $html, string $selector, string $attribute): ?string
    {
        return self::getNode($html,$selector)?->getAttribute($attribute);
    }
}