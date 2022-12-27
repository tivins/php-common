<?php

namespace Core;

use Tivins\Core\StrUtil;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class StrUtilTest extends TestCase
{

    public function testMarkdown()
    {
        $html = StrUtil::markdown("hello **world**");
        assertEquals('<p>hello <strong>world</strong></p>', $html);
    }
}
