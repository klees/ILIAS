<?php declare(strict_types=1);

/* Copyright (c) 2019 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Tree\Node;

use ILIAS\UI\Component\Symbol\Icon\Icon;

/**
 * This describes a very basic Tree Node.
 */
interface Simple extends Node, AsyncNode
{
    /**
     * Get the icon for this Node.
     */
    public function getIcon() : ?Icon;
}
