<?php
/* Copyright (c) 2016 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Setup\CLI;

use ILIAS\Setup\ArrayEnvironment;
use ILIAS\Setup\Environment;
use ILIAS\Setup\Migration;
use ILIAS\Setup\NoConfirmationException;
use ILIAS\Setup\Objective;
use ILIAS\Setup\ObjectiveCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Migration command.
 */
class MigrateCommand extends Command
{
    use HasAgent;
    use HasConfigReader;
    use ObjectiveHelper;

    protected static $defaultName = "migrate";

    /**
     * var Objective[]
     */
    protected $preconditions;

    /**
     * @var callable    $lazy_agent    must return a Setup\Agent
     * @var Objective[] $preconditions will be achieved before command invocation
     */
    public function __construct(callable $lazy_agent, ConfigReader $config_reader, array $preconditions)
    {
        parent::__construct();
        $this->lazy_agent    = $lazy_agent;
        $this->config_reader = $config_reader;
        $this->preconditions = $preconditions;
    }

    public function configure()
    {
        $this->setDescription("Starts and manages migrations needed after an update of ILIAS");
        $this->addArgument("config", InputArgument::REQUIRED, "Configuration file for the installation");
        $this->addOption("config", null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, "Define fields in the configuration file that should be overwritten, e.g. \"a.b.c=foo\"", []);
        $this->addOption("yes", "y", InputOption::VALUE_NONE, "Confirm every message of the installation.");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new IOWrapper($input, $output);
        $io->printLicenseMessage();
        $io->title("Trigger migrations in ILIAS");

        $agent = $this->getAgent();

        $config = $this->readAgentConfig($agent, $input);

        $objective = new ObjectiveCollection(
            "Handle migrations in ILIAS after update",
            false,
            $agent->getInstallObjective($config),
            $agent->getUpdateObjective()
        );

        $migrations = $agent->getMigrations();

        $objective = new ObjectiveCollection(
            "Handle migrations in ILIAS after update",
            false,
            ...array_map(static function (Migration $m) {
                return new Objective\MigrationObjective($m);
            }, $migrations)
        );

        if (count($this->preconditions) > 0) {
            $objective = new Objective\ObjectiveWithPreconditions(
                $objective,
                ...$this->preconditions
            );
        }

        $environment = new ArrayEnvironment([
            Environment::RESOURCE_ADMIN_INTERACTION => $io
        ]);
        $environment = $this->addAgentConfigsToEnvironment($agent, $config, $environment);
        // ATTENTION: This is bad because we strongly couple this generic command
        // to something very specific here. This can go away once we have got rid of
        // everything related to clients, since we do not need that client-id then.
        // This will require some more work, though.
        $common_config = $config->getConfig("common");
        $environment   = $environment->withResource(
            Environment::RESOURCE_CLIENT_ID,
            $common_config->getClientId()
        );

        try {
            $this->achieveObjective($objective, $environment, $io);
            $io->success("Installation complete. Thanks and have fun!");
        } catch (NoConfirmationException $e) {
            $io->error("Aborting Installation, a necessary confirmation is missing:\n\n" . $e->getRequestedConfirmation());
        }
    }
}
