<?php declare(strict_types=1);

namespace ILIAS\UI\Implementation\Component\Symbol\Avatar;

use ILIAS\UI\Component as C;

class Letter extends Avatar implements C\Symbol\Avatar\Letter
{
    public function getAbbreviation() : string
    {
        return (substr($this->getUsername(), 0, 2));
    }

    public function getBackgroundColorVariant() : int
    {
        return ((crc32($this->getAbbreviation()) % 26) + 1);
    }
}
