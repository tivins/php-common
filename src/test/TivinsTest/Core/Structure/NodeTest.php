<?php

namespace TivinsTest\Core\Structure;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Structure\Node;

class NodeTest extends TestCase
{
    public function testGeneral()
    {
        $rootNode = new Node();
        $this->assertEquals(1, $rootNode->id);
        $this->assertEquals('#1', (string) $rootNode);

        $this->assertEquals(json_encode([
            'parent' => 0,
            'first_child' => 0,
            'prev' => 0,
            'next' => 0,
        ]), json_encode($rootNode));

        $newNode = new Node();
        $rootNode->set_first_child($newNode);
        $this->assertEquals(json_encode([
            'parent' => 0,
            'first_child' => 2,
            'prev' => 0,
            'next' => 0,
        ]), json_encode($rootNode));
        $this->assertEquals(json_encode([
            'parent' => 1,
            'first_child' => 0,
            'prev' => 0,
            'next' => 0,
        ]), json_encode($newNode));

        $this->assertEquals($newNode, $rootNode->getFirstChild());
    }
}