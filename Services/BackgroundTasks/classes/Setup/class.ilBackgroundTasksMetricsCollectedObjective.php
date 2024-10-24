<?php

use ILIAS\Setup;

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/
class ilBackgroundTasksMetricsCollectedObjective extends Setup\Metrics\CollectedObjective
{
    /**
     * @return \ilIniFilesLoadedObjective[]
     */
    public function getTentativePreconditions(Setup\Environment $environment) : array
    {
        return [
            new ilIniFilesLoadedObjective()
        ];
    }

    public function collectFrom(Setup\Environment $environment, Setup\Metrics\Storage $storage) : void
    {
        $ini = $environment->getResource(Setup\Environment::RESOURCE_ILIAS_INI);
        if (!$ini) {
            return;
        }

        $storage->storeConfigText(
            "type",
            $ini->readVariable("background_tasks", "concurrency"),
            "The type of execution used for background tasks"
        );
        $storage->storeConfigGauge(
            "max_number_of_concurrent_tasks",
            (int)$ini->readVariable("background_tasks", "number_of_concurrent_tasks"),
            "The maximum amount of concurrent tasks used to run background tasks."
        );
    }
}
