<?php declare(strict_types=1);

/* Copyright (c) 2017 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Button;

use ILIAS\UI\Component as C;
use ILIAS\Data as D;
use ILIAS\Data\Color;

class Tag extends Button implements C\Button\Tag
{
    private static array $relevance_levels = array(
         self::REL_VERYLOW,
         self::REL_LOW,
         self::REL_MID,
         self::REL_HIGH,
         self::REL_VERYHIGH
    );

    protected string $relevance = self::REL_VERYHIGH;
    protected ?Color $bgcol = null;
    protected ?Color $forecol = null;

    /**
     * @var string[]
     */
    protected array $additional_classes = [];

    /**
     * @inheritdoc
     */
    public function withRelevance(string $relevance) : Tag
    {
        $this->checkArgIsElement('relevance', $relevance, self::$relevance_levels, 'relevance');
        $clone = clone $this;
        $clone->relevance = $relevance;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getRelevance() : string
    {
        return $this->relevance;
    }

    public function getRelevanceClass() : string
    {
        return self::$relevance_levels[$this->relevance - 1];
    }

    /**
     * @inheritdoc
     */
    public function withBackgroundColor(Color $col) : C\Button\Tag
    {
        $clone = clone $this;
        $clone->bgcol = $col;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getBackgroundColor() : ?Color
    {
        return $this->bgcol;
    }

    /**
     * @inheritdoc
     */
    public function withForegroundColor(Color $col) : C\Button\Tag
    {
        $clone = clone $this;
        $clone->forecol = $col;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getForegroundColor() : ?Color
    {
        if (is_null($this->forecol) && is_null($this->bgcol) === false) {
            $col_val = $this->bgcol->isDark() ? '#fff' : '#000';
            $df = new D\Factory();
            return $df->color($col_val);
        }
        return $this->forecol;
    }

    /**
     * @inheritdoc
     */
    public function withClasses(array $classes) : C\Button\Tag
    {
        $classes = $this->toArray($classes);
        foreach ($classes as $class) {
            $this->checkStringArg('classes', $class);
        }
        $clone = clone $this;
        $clone->additional_classes = $classes;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getClasses() : array
    {
        if (!$this->additional_classes) {
            return array();
        }
        return $this->additional_classes;
    }
}
