<?php declare(strict_types=1);

/* Copyright (c) 2019 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

require_once("libs/composer/vendor/autoload.php");
require_once(__DIR__ . "/../../Base.php");

use ILIAS\UI\Implementation\Component\Tree\Tree;
use ILIAS\UI\Component\Tree\TreeRecursion;
use ILIAS\UI\Component\Tree\Node\Factory;
use ILIAS\UI\Component\Tree\Node\Node;

/**
 * Dummy-implementation for testing
 */
class TestingTree extends Tree
{
}

/**
 * Tests for the (Base-)Tree.
 */
class TreeTest extends ILIAS_UI_TestBase
{
    public function testWrongConstruction() : void
    {
        $this->expectException(ArgumentCountError::class);
        $tree = new TestingTree();
    }

    public function testWrongTypeConstruction() : void
    {
        $this->expectException(TypeError::class);
        $tree = new TestingTree('something');
    }

    public function testConstruction() : TestingTree
    {
        $label = "label";
        $recursion = new class implements TreeRecursion {
            public function getChildren($record, $environment = null) : array
            {
                return [];
            }

            public function build(
                Factory $factory,
                $record,
                $environment = null
            ) : Node {
            }
        };

        $tree = new TestingTree($label, $recursion);
        $this->assertInstanceOf("ILIAS\\UI\\Component\\Tree\\Tree", $tree);

        return $tree;
    }

    /**
     * @depends testConstruction
     */
    public function testGetLabel(TestingTree $tree) : void
    {
        $this->assertEquals("label", $tree->getLabel());
    }

    /**
     * @depends testConstruction
     */
    public function testGetRecursion(TestingTree $tree) : void
    {
        $this->assertInstanceOf("ILIAS\\UI\\Component\\Tree\\TreeRecursion", $tree->getRecursion());
    }

    /**
     * @depends testConstruction
     */
    public function testWithEnvironment(TestingTree $tree) : void
    {
        $env = ['key1' => 'val1', 'key2' => 2];
        $this->assertEquals($env, $tree->withEnvironment($env)->getEnvironment());
    }

    /**
     * @depends testConstruction
     */
    public function testWithData(TestingTree $tree) : void
    {
        $data = ['entry1', 'entry2'];
        $this->assertEquals($data, $tree->withData($data)->getData());
    }

    /**
     * @depends testConstruction
     */
    public function testWithHighlightOnNodeClick(TestingTree $tree) : void
    {
        $this->assertFalse($tree->getHighlightOnNodeClick());
        $this->assertTrue($tree->withHighlightOnNodeClick(true)->getHighlightOnNodeClick());
    }

    /**
     * @depends testConstruction
     */
    public function testWithIsSubTree(TestingTree $tree) : void
    {
        $this->assertFalse($tree->isSubTree());
        $this->assertTrue($tree->withIsSubTree(true)->isSubTree());
    }
}
