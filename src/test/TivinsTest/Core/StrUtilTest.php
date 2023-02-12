<?php

namespace TivinsTest\Core;

use PHPUnit\Framework\TestCase;
use Tivins\Core\StrUtil;
use function PHPUnit\Framework\assertEquals;

class StrUtilTest extends TestCase
{

    public function testMarkdown()
    {
        $html = StrUtil::markdown("hello **world**");
        assertEquals('<p>hello <strong>world</strong></p>', $html);
    }

    public function testMarkdownThin()
    {
        $html = StrUtil::markdown('hello **world**', true);
        assertEquals('hello <strong>world</strong>', $html);
    }
}
