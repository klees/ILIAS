<?php declare(strict_types=1);

/* Copyright (c) 2016 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Setup\CLI;

use ILIAS\Setup\AgentFinder;
use ILIAS\Setup\ArrayEnvironment;
use ILIAS\Setup\Environment;
use ILIAS\Setup\Objective;
use ILIAS\Setup\Objective\ObjectiveWithPreconditions;
use ILIAS\Setup\NoConfirmationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Update command.
 */
class UpdateCommand extends Command
{
    use HasAgent;
    use HasConfigReader;
    use ObjectiveHelper;

    protected static $defaultName = "update";

    /**
     * var Objective[]
     */
    protected array $preconditions;

    /**
     * @var Objective[] $preconditions will be achieved before command invocation
     */
    public function __construct(AgentFinder $agent_finder, ConfigReader $config_reader, array $preconditions)
    {
        parent::__construct();
        $this->agent_finder = $agent_finder;
        $this->config_reader = $config_reader;
        $this->preconditions = $preconditions;
    }

    protected function configure()
    {
        $this->setDescription("Updates an existing ILIAS installation");
        $this->addArgument("config", InputArgument::OPTIONAL, "Configuration file for the update");
        $this->addOption("config", null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "Define fields in the configuration file that should be overwritten, e.g. \"a.b.c=foo\"", []);
        $this->addOption("ignore-db-update-messages", null, InputOption::VALUE_NONE, "Ignore messages from the database update steps.");
        $this->addOption("yes", "y", InputOption::VALUE_NONE, "Confirm every message of the update.");
        $this->configureCommandForPlugins();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        // ATTENTION: This is a hack to get around the usage of the echo/exit pattern in
        // the setup for the command line version of the setup. Do not use this.
        if ($input->hasOption("ignore-db-update-messages") && $input->getOption("ignore-db-update-messages")) {
            define("ILIAS_SETUP_IGNORE_DB_UPDATE_STEP_MESSAGES", true);
        }

        $io = new IOWrapper($input, $output);
        $io->printLicenseMessage();
        $io->title("Update ILIAS");

        $agent = $this->getRelevantAgent($input);

        if ($input->getArgument("config")) {
            $config = $this->readAgentConfig($agent, $input);
        } else {
            $config = null;
        }

        $objective = $agent->getUpdateObjective($config);
        if (count($this->preconditions) > 0) {
            $objective = new ObjectiveWithPreconditions(
                $objective,
                ...$this->preconditions
            );
        }

        $environment = new ArrayEnvironment([
            Environment::RESOURCE_ADMIN_INTERACTION => $io
        ]);
        if ($config) {
            $environment = $this->addAgentConfigsToEnvironment($agent, $config, $environment);
        }

        try {
            $this->achieveObjective($objective, $environment, $io);
            $io->success("Update complete. Thanks and have fun!");
        } catch (NoConfirmationException $e) {
            $io->error("Aborting Update, a necessary confirmation is missing:\n\n" . $e->getRequestedConfirmation());
        }

        return 0;
    }
}
