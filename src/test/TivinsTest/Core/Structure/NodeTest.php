<?php

namespace TivinsTest\Core\Structure;

use PHPUnit\Framework\TestCase;
use Tivins\Core\Structure\Node;

class NodeItem extends Node {
    public function uniqueFunction(): string { return 'test1'; }
}

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
        $this->assertEquals($rootNode, $newNode->getParent());
        $this->assertTrue($rootNode->hasChildren());
        $this->assertFalse($rootNode->hasNext());
        $this->assertFalse($rootNode->hasParent());

        $nextNode = new Node();
        $newNode->setNext($nextNode);
        $this->assertEquals(json_encode([
            'parent' => 1,
            'first_child' => 0,
            'prev' => 0,
            'next' => 3,
        ]), json_encode($newNode));
    }

    public function testExtends()
    {
        $root = new NodeItem();
        $child = new NodeItem();
        $anotherNode = new Node();
        $root->appendChild($anotherNode);
        $root->appendChild($child);
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($anotherNode->hasParent());
        $this->assertTrue($child->hasParent());
        $this->assertEquals($child, $anotherNode->getNext());
        $this->assertEquals('test1', $child->uniqueFunction());
        $this->expectError();
        $this->assertEquals('test1', $anotherNode->uniqueFunction());
    }
}