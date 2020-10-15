<?php

/* Copyright (c) 2019 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Setup;

/**
 * A migration objective
 */
interface Migration // extends Objective
{
    public const INFINITE = -1;

    /**
     * tell what the default value for a migration step is.
     * Return Migration::INFINITE if all units need to be migrated at once.
     * @return int
     */
    public function getDefaultIterationStep() : int;

    /**
     * Objectives the migration depend on.
     *
     * @throw UnachievableException if the objective is not achievable
     *
     * @return Objective[]
     */
    public function getPreconditions(Environment $environment) : array;

    /**
     *  Run one step of the migration.
     */
    public function step(Environment $environment) : void;

    /**
     * Count up how many "things" need to be migrated. This helps the admin to
     * decide how big he can create the steps and also how long a migration takes
     * @return int
     */
    public function getRemainingAmountOfUnitsToMigrate() : int;
}
