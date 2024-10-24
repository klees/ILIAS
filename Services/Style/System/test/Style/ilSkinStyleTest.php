<?php

declare(strict_types=1);

require_once('libs/composer/vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class ilSkinStyleTest extends TestCase
{
    protected ilSkinStyle $style1;

    public function testStyleNameAndId()
    {
        $this->style1 = new ilSkinStyle('style1', 'Style 1');
        $this->assertEquals('style1', $this->style1->getId());
        $this->assertEquals('Style 1', $this->style1->getName());
    }

    public function testStyleProperties()
    {
        $this->style1 = new ilSkinStyle('style1', 'Style 1');
        $this->style1->setId('id');
        $this->style1->setName('name');
        $this->style1->setCssFile('css');
        $this->style1->setImageDirectory('image');
        $this->style1->setSoundDirectory('sound');

        $this->assertEquals('id', $this->style1->getId());
        $this->assertEquals('name', $this->style1->getName());
        $this->assertEquals('css', $this->style1->getCssFile());
        $this->assertEquals('image', $this->style1->getImageDirectory());
        $this->assertEquals('sound', $this->style1->getSoundDirectory());
    }
}
