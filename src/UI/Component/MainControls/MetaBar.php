<?php declare(strict_types=1);

/* Copyright (c) 2018 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\MainControls;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Signal;
use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Component\Button;
use ILIAS\UI\Component\MainControls\Slate\Slate;

/**
 * This describes the MetaBar.
 */
interface MetaBar extends Component, JavaScriptBindable
{
    /**
     * Append an entry.
     *
     * @param Button\Bulky|Slate $entry
     * @throws \InvalidArgumentException 	if $id is already taken
     */
    public function withAdditionalEntry(string $id, $entry) : MetaBar;

    /**
     * @return array <string, Bulky|Slate>
     */
    public function getEntries() : array;

    /**
     * The Signal is triggered when any Entry is being clicked.
     */
    public function getEntryClickSignal() : Signal;

    /**
     * This signal disengages all slates when triggered.
     */
    public function getDisengageAllSignal() : Signal;

    /**
     * Get a copy of this MetaBar without any entries.
     */
    public function withClearedEntries() : MetaBar;
}
