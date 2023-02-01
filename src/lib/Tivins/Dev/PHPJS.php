<?php

namespace Tivins\Dev;

use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Property;

class PHPJS extends PHPHighlight
{
    protected string $propertyFetchSeparator = '.';
    protected string $classConstFetchSeparator = '.';
    protected function parseVar(Variable $expr): string
    {
        return $this->getSpan($expr->name, 'nv');
    }

    protected function parseParam(Param $param): string
    {
        return $this->parseNodeExpr($param->var);
    }
    protected function getAccess(mixed $item): string
    {
        $access = '';
        if (method_exists($item, 'isPrivate') && $item->isPrivate())
            $access = '#';
        return $access ? $this->getSpan($access, 'k') : '';
    }
    protected function parseProperty(Property $property): string
    {
        $html = '';
        //

        $html .= $this->getAccess($property);
        $html .= $property->props[0]->name;
        $html .= ';';
        return
            $this->getIndentLine('/** @type {'.$property->type.'} */')
            .$this->getIndentLine($html);
    }

}