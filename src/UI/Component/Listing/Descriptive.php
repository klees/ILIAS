<?php declare(strict_types=1);

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Listing;

/**
 * Interface Descriptive
 * @package ILIAS\UI\Component\Listing
 */
interface Descriptive extends Listing
{
    /**
     * Sets a key value pair as items for the list. Key is used as title and value as content.
     * @param array $items string => Component | string
     */
    public function withItems(array $items) : Descriptive;

    /**
     * Gets the key value pair as items for the list. Key is used as title and value as content.
     * @return array $items string => Component | string
     */
    public function getItems() : array;
}
