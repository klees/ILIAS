<?php
/* Copyright (c) 2016 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Setup\CLI;

use ILIAS\Setup\Agent;
use ILIAS\Setup\AgentCollection;
use ILIAS\Setup\ArrayEnvironment;
use ILIAS\Setup\Config;
use ILIAS\Setup\Environment;
use ILIAS\Setup\NoConfirmationException;
use ILIAS\Setup\Objective;
use ILIAS\Setup\Objective\ObjectiveWithPreconditions;
use ILIAS\Setup\ObjectiveCollection;
use ILIAS\Setup\ObjectiveIterator;
use ILIAS\Setup\UnachievableException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Migrate command.
 */
class MigrateCommand extends BaseCommand
{
    protected static $defaultName = "migrate";

    public function configure()
    {
        parent::configure();
        $this->setDescription("Triggers ans manages migrations in an existing ILIAS installation");
    }

    protected function printIntroMessage(IOWrapper $io)
    {
        $io->title("Trigger Migrations in ILIAS");
    }

    protected function printOutroMessage(IOWrapper $io)
    {
        $io->success("All Migrations complete. Thanks and have fun!");
    }

    protected function buildEnvironment(Agent $agent, ?Config $config, IOWrapper $io) : Environment
    {
        $environment = new ArrayEnvironment([
            Environment::RESOURCE_ADMIN_INTERACTION => $io
        ]);

        if ($agent instanceof AgentCollection && $config) {
            foreach ($config->getKeys() as $k) {
                $environment = $environment->withConfigFor($k, $config->getConfig($k));
            }
        }

        return $environment;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // TO DISCUSS:
        // Wir könnten hier nach dem genau gleichn Prinzip verfahren wie sonst
        // oder eben hier explizit immer wieder nach den steps fragen:

        $io = new IOWrapper($input, $output, $this->shouldSayYes($input));

        $this->printLicenseMessage($io, $input);

        $this->printIntroMessage($io);

        $config = $this->readAgentConfig($this->getAgent(), $input);
        $environment = $this->buildEnvironment($this->getAgent(), $config, $io);
        $goal = $this->getObjective($this->getAgent(), $config);
        if (count($this->preconditions) > 0) {
            $goal = new ObjectiveWithPreconditions(
                $goal,
                ...$this->preconditions
            );
        }
        $goals = new ObjectiveIterator($environment, $goal);

        try {
            while ($goals->valid()) {
                $current = $goals->current();
                if (!$current->isApplicable($environment)) {
                    $goals->next();
                    continue;
                }
                $io->startObjective($current->getLabel(), $current->isNotable());
                try {
                    // TO DISCUSS:
                    // Hier müssten wir dann entsprechend entweder einen anderen
                    // aufruf starten, oder eine innerhalb von achieve immer wieder process
                    // aufrufen bis fertig, oder mit NotYetFinishedException arbeiten ...


                    $environment = $current->achieve($environment);
                    $io->finishedLastObjective($current->getLabel(), $current->isNotable());
                    $goals->setEnvironment($environment);
                } catch (UnachievableException $e) {
                    $goals->markAsFailed($current);
                    $io->error($e->getMessage());
                    $io->failedLastObjective($current->getLabel());
                }
                $goals->next();
            }
            $this->printOutroMessage($io);
        } catch (NoConfirmationException $e) {
            $io->error("Aborting Setup, a necessary confirmation is missing:\n\n" . $e->getRequestedConfirmation());
        }
    }

    protected function getObjective(Agent $agent, ?Config $config) : Objective
    {
        return new ObjectiveCollection(
            "Migrate ILIAS",
            false,
            $agent->getMigrations($config)
        );
    }
}
