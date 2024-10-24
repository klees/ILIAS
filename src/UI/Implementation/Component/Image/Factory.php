<?php declare(strict_types=1);

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Image;

use ILIAS\UI\Component\Image as I;

/**
 * Class Factory
 * @package ILIAS\UI\Implementation\Component\Image
 */
class Factory implements I\Factory
{
    /**
     * @inheritdoc
     */
    public function standard(string $src, string $alt) : I\Image
    {
        return new Image(I\Image::STANDARD, $src, $alt);
    }

    /**
     * @inheritdoc
     */
    public function responsive(string $src, string $alt) : I\Image
    {
        return new Image(I\Image::RESPONSIVE, $src, $alt);
    }
}
