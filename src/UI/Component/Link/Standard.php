<?php declare(strict_types=1);

/* Copyright (c) 2017 Alexander Killing <killing@leifos.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Link;

/**
 * Standard link
 */
interface Standard extends Link
{
    /**
     * Get the label of the link
     */
    public function getLabel() : string;
}
