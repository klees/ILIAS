<?php declare(strict_types=1);

/* Copyright (c) 2017 Ralph Dittrich <dittrich@qualitus.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Chart\ProgressMeter;

use ILIAS\UI\Component as C;
use ILIAS\UI\Implementation\Component\ComponentHelper;

/**
 * Class ProgressMeter
 * @package ILIAS\UI\Implementation\Component\Chart\ProgressMeter
 */
class ProgressMeter implements C\Chart\ProgressMeter\ProgressMeter
{
    use ComponentHelper;

    protected int $maximum;
    private int $required;
    protected int $main;
    protected int $comparison;

    public function __construct(int $maximum, int $main, int $required = null, int $comparison = null)
    {
        $this->maximum = $maximum;
        $this->main = $this->getSafe($main);

        if ($required != null) {
            $this->required = $this->getSafe($required);
        } else {
            $this->required = $this->getSafe($maximum);
        }
        if ($comparison != null) {
            $this->comparison = $this->getSafe($comparison);
        } else {
            $this->comparison = 0;
        }
    }

    /**
     * @inheritdoc
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @inheritdoc
     */
    public function getRequired()
    {
        return $this->getSafe($this->required);
    }

    /**
     * Get required value as percent
     */
    public function getRequiredAsPercent() : int
    {
        return $this->getAsPercentage($this->required);
    }

    /**
     * @inheritdoc
     */
    public function getMainValue()
    {
        return $this->getSafe($this->main);
    }

    /**
     * Get main value as percent
     */
    public function getMainValueAsPercent() : int
    {
        return $this->getAsPercentage($this->main);
    }

    /**
     * Get integer value "1" if a value is negative or "maximum" if value is more than maximum
     */
    protected function getSafe(int $int) : int
    {
        return (($int < 0) ? 0 : ($int > $this->getMaximum() ? $this->getMaximum() : $int));
    }

    /**
     * get an integer value as percent value
     */
    protected function getAsPercentage(int $int) : int
    {
        return (int) round(100 / $this->getMaximum() * $this->getSafe($int), 0, PHP_ROUND_HALF_UP);
    }
}
