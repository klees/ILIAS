<?php declare(strict_types=1);

/* Copyright (c) 2019 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

use ILIAS\Setup;

class ilMakeInstallationAccessibleObjective extends ilSetupObjective
{
    public function getHash() : string
    {
        return hash("sha256", self::class);
    }

    public function getLabel() : string
    {
        return "The installation is accessible.";
    }

    public function isNotable() : bool
    {
        return true;
    }

    public function getPreconditions(Setup\Environment $environment) : array
    {
        $db_config = $environment->getConfigFor("database");
        return [
            new \ilIniFilesPopulatedObjective(),
            new ilDatabasePopulatedObjective($db_config),
            new \ilSettingsFactoryExistsObjective()
        ];
    }

    public function achieve(Setup\Environment $environment) : Setup\Environment
    {
        $factory = $environment->getResource(Setup\Environment::RESOURCE_SETTINGS_FACTORY);
        $settings = $factory->settingsFor("common");

        $settings->set("setup_ok", "1");

        $client_ini = $environment->getResource(Setup\Environment::RESOURCE_CLIENT_INI);

        $client_ini->setVariable("client", "access", '1');

        if (!$client_ini->write()) {
            throw new Setup\UnachievableException("Could not write client.ini.php");
        }

        return $environment;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(Setup\Environment $environment) : bool
    {
        $factory = $environment->getResource(Setup\Environment::RESOURCE_SETTINGS_FACTORY);
        $settings = $factory->settingsFor("common");
        $client_ini = $environment->getResource(Setup\Environment::RESOURCE_CLIENT_INI);

        return
            $settings->get("setup_ok") != 1 ||
            $client_ini->readVariable("client", "access") != true
        ;
    }
}
