<?php declare(strict_types=1);

/* Copyright (c) 2016 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Button;

use ILIAS\UI\Component\Button as B;
use ILIAS\UI\Component\Signal;
use ILIAS\UI\Component\Symbol\Symbol;
use ILIAS\UI\NotImplementedException;

class Factory implements B\Factory
{
    /**
     * @inheritdoc
     */
    public function standard(string $label, $action) : B\Standard
    {
        return new Standard($label, $action);
    }

    /**
     * @inheritdoc
     */
    public function primary(string $label, $action) : B\Primary
    {
        return new Primary($label, $action);
    }

    /**
     * @inheritdoc
     */
    public function close() : B\Close
    {
        return new Close();
    }

    /**
     * @inheritdoc
     */
    public function minimize() : B\Minimize
    {
        return new Minimize();
    }

    /**
     * @inheritdoc
     */
    public function tag(string $label, $action) : B\Tag
    {
        return new Tag($label, $action);
    }

    /**
     * @inheritdoc
     */
    public function shy(string $label, $action) : B\Shy
    {
        return new Shy($label, $action);
    }

    /**
     * @inheritdoc
     */
    public function month(string $default) : B\Month
    {
        return new Month($default);
    }

    /**
     * @inheritdoc
     */
    public function bulky(Symbol $icon_or_glyph, string $label, string $action) : B\Bulky
    {
        return new Bulky($icon_or_glyph, $label, $action);
    }

    /**
     * @inheritdoc
     */
    public function toggle(
        string $label,
        $on_action,
        $off_action,
        bool $is_on = false,
        Signal $click_signal = null
    ) : B\Toggle {
        return new Toggle($label, $on_action, $off_action, $is_on, $click_signal);
    }
}
