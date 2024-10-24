<?php declare(strict_types=1);

/* Copyright (c) 2021 - Nils Haagen <nils.haagen@concepts-and-training.de> - Extended GPL, see LICENSE */

use PHPUnit\Framework\TestCase;
use ILIAS\KioskMode\ControlBuilder;

class LSTOCBuilderTest extends TestCase
{
    public function testConstruction()
    {
        $cb = $this->createMock(LSControlBuilder::class);
        $tb = new LSTOCBuilder($cb, '');
        $this->assertEquals(
            json_encode(["label" => "","command" => "","parameter" => null,"state" => null,"childs" => []]),
            $tb->toJSON()
        );
    }

    public function testRecursion()
    {
        $cb = $this->createMock(LSControlBuilder::class);
        $tb = new LSTOCBuilder($cb, '');
        $tb
            ->node('node1')
                ->item('item1.1', 1)
                ->item('item1.2', 2)
            ->end()
            ->item('item2', 3)
            ->node('node3')
                ->item('item3.1', 4)
                ->node('node3.2', 5)
                    ->item('item3.2.1', 6)
                ->end()
            ->end()
        ->end();

        $expected = [
            "label" => "","command" => "","parameter" => null,"state" => null,"childs" => [
                ["label" => "node1","command" => "","parameter" => null,"state" => null,"childs" => [
                    ["label" => "item1.1","command" => "","parameter" => 1,"state" => null,"current" => false],
                    ["label" => "item1.2","command" => "","parameter" => 2,"state" => null,"current" => false]
                ]],
            ["label" => "item2","command" => "","parameter" => 3,"state" => null, "current" => false],
            ["label" => "node3","command" => "","parameter" => null,"state" => null,"childs" => [
                ["label" => "item3.1","command" => "","parameter" => 4,"state" => null,"current" => false],
                ["label" => "node3.2","command" => "","parameter" => 5,"state" => null,"childs" => [
                    ["label" => "item3.2.1","command" => "","parameter" => 6,"state" => null,"current" => false]
                ]
            ]]]]];

        $this->assertEquals(
            json_encode($expected),
            $tb->toJSON()
        );
    }

    public function testToCEnd()
    {
        $cb = $this->createMock(LSControlBuilder::class);
        $tb = new LSTOCBuilder($cb, '');
        $tb = $tb->end();
        $this->assertInstanceOf(ControlBuilder::class, $tb);
    }
}
