<?php declare(strict_types=1);

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Chart;

use ILIAS\UI\Component as C;

/**
 * Class Factory
 * @package ILIAS\UI\Implementation\Component\Listing
 */
class Factory implements C\Chart\Factory
{
    protected C\Chart\ProgressMeter\Factory $progressmeter_factory;

    public function __construct(C\Chart\ProgressMeter\Factory $progressmeter_factory)
    {
        $this->progressmeter_factory = $progressmeter_factory;
    }

    /**
     * @inheritdoc
     */
    public function scaleBar(array $items) : C\Chart\ScaleBar
    {
        return new ScaleBar($items);
    }

    /**
     * @inheritdoc
     */
    public function progressMeter() : C\Chart\ProgressMeter\Factory
    {
        return $this->progressmeter_factory;
    }
}
