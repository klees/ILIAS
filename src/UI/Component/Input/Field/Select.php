<?php declare(strict_types=1);

/* Copyright (c) 2018 Jesús López <lopez@leifos.com> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Input\Field;

/**
 * This describes select field.
 */
interface Select extends FilterInput
{
    /**
     * @return array<string,string> of key=>value options.
     */
    public function getOptions() : array;
}
