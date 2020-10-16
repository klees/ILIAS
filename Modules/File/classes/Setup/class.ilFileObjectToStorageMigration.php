<?php

use ILIAS\Setup;
use ILIAS\Setup\Environment;

class ilFileObjectToStorageMigration implements Setup\Migration
{
    /**
     * @inheritDoc
     */
    public function getLabel() : string
    {
        return "Migration of File-Objects to Storage service";
    }

    /**
     * @inheritDoc
     */
    public function getDefaultIterationStep() : int
    {
        return 100;
    }

    /**
     * @inheritDoc
     */
    public function getPreconditions(Environment $environment) : array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function step(Environment $environment) : void
    {
        usleep(120000);
    }

    /**
     * @inheritDoc
     */
    public function getRemainingAmountOfUnitsToMigrate() : int
    {
        return 86574854;
    }

}
