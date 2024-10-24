<?php declare(strict_types=1);

/* Copyright (c) 2016 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Setup\CLI;

use ILIAS\Setup\AgentFinder;
use ILIAS\Setup\ArrayEnvironment;
use ILIAS\Setup\Objective\Tentatively;
use ILIAS\Setup\Metrics;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ILIAS\Setup\Agent;

/**
 * Command to output status information about the installation.
 */
class StatusCommand extends Command
{
    use HasAgent;
    use ObjectiveHelper;

    protected static $defaultName = "status";

    public function __construct(AgentFinder $agent_finder)
    {
        parent::__construct();
        $this->agent_finder = $agent_finder;
    }

    protected function configure()
    {
        $this->setDescription("Collect and show status information about the installation.");
        $this->configureCommandForPlugins();
    }


    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $agent = $this->getRelevantAgent($input);

        $output->write($this->getMetrics($agent)->toYAML() . "\n");
        
        return 0;
    }

    public function getMetrics(Agent $agent) : Metrics\Metric
    {
        // ATTENTION: Don't do this (in general), please have a look at the comment
        // in ilIniFilesLoadedObjective.
        \ilIniFilesLoadedObjective::$might_populate_ini_files_as_well = false;

        $environment = new ArrayEnvironment([]);
        $storage = new Metrics\ArrayStorage();
        $objective = new Tentatively(
            $agent->getStatusObjective($storage)
        );

        $this->achieveObjective($objective, $environment);

        $metric = $storage->asMetric();
        list($config, $other) = $metric->extractByStability(Metrics\Metric::STABILITY_CONFIG);
        if ($other) {
            $values = $other->getValue();
        } else {
            $values = [];
        }
        if ($config) {
            $values["config"] = $config;
        }

        return new Metrics\Metric(
            Metrics\Metric::STABILITY_MIXED,
            Metrics\Metric::TYPE_COLLECTION,
            $values
        );
    }
}
