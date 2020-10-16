<?php

namespace ILIAS\Setup\Objective;

use ILIAS\Setup;

/**
 * handles a Migration
 */
class MigrationObjective implements Setup\Objective
{
    /**
     * @var Setup\Migration
     */
    protected $migration;

    /**
     * MigrationObjective constructor.
     * @param Setup\Migration $migration
     */
    public function __construct(Setup\Migration $migration)
    {
        $this->migration = $migration;
    }

    /**
     * Uses hashed Path.
     * @inheritdocs
     */
    public function getHash() : string
    {
        return hash("sha256", self::class . '' . get_class($this->migration));
    }

    /**
     * @inheritdocs
     */
    public function getLabel() : string
    {
        return $this->migration->getLabel();
    }

    /**
     * Defaults to 'true'.
     * @inheritdocs
     */
    public function isNotable() : bool
    {
        return true;
    }

    /**
     * @inheritdocs
     */
    public function getPreconditions(Setup\Environment $environment) : array
    {
        return [];
    }

    /**
     * @inheritdocs
     */
    public function achieve(Setup\Environment $environment) : Setup\Environment
    {
        /**
         * @var $io Setup\CLI\IOWrapper
         */
        $io = $environment->getResource(Setup\Environment::RESOURCE_ADMIN_INTERACTION);

        $steps = $this->migration->getDefaultIterationStep();
        $io->confirmOrDeny("Run {$steps} steps in {$this->getLabel()}? This may take a while depending on the migration and the installation.");
        $io->inform("Trigger {$steps} steps in {$this->getLabel()}");
        $step = 0;
        $io->startProgress($steps);
        while ($step < $steps) {
            $io->advanceProgress();
            $this->migration->step($environment);
            $step++;
        }
        $io->stopProgress();
        $io->inform("there are {$this->migration->getRemainingAmountOfUnitsToMigrate()} steps remaining. run again to proceed.");

        return $environment;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(Setup\Environment $environment) : bool
    {
        return $this->migration->getRemainingAmountOfUnitsToMigrate() > 0;
    }
}
