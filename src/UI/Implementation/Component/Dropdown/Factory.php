<?php declare(strict_types=1);

/* Copyright (c) 2017 Alexander Killing <killing@leifos.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Dropdown;

use ILIAS\UI\Component\Dropdown as D;

class Factory implements D\Factory
{
    /**
     * @inheritdoc
     */
    public function standard(array $items) : D\Standard
    {
        return new Standard($items);
    }
}
