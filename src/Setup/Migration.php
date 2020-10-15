<?php

/* Copyright (c) 2019 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Setup;

/**
 * A migration objective
 */
interface Migration extends Objective
{
    public const INFINITE = -1;

    /**
     * tell what the default value for a migration step is.
     * Return Migration::INFINITE if all units need to be migrated at once.
     * @return int
     */
    public function getDefaultIterationStep() : int;

    /**
     * proceed with the migration for the number of units specified. if no
     * number is given, use the default or all if the default is higher than the
     * total number of units to be migrated
     * @param int|null $next
     */
    public function proceed(int $next = null) : void;

    /**
     * @return float with a maximum of 1 (finished)
     */
    public function getProgress() : float;

    /**
     * Count up how many "things" need to be migrated. This helps the admin to
     * decide how big he can create the steps and also how long a migration takes
     * @return int
     */
    public function getRemainingAmountOfUnitsToMigrate() : int;
}
