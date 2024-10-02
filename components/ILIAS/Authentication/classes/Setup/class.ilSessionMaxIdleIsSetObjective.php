<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

use ILIAS\Setup;

class ilSessionMaxIdleIsSetObjective implements Setup\Objective
{
    public function __construct(
        protected ilAuthenticationSetupConfig $config
    ) {
    }

    public function getHash(): string
    {
        return hash("sha256", self::class);
    }

    public function getLabel(): string
    {
        return "Ensures 'session_max_idle' is set properly";
    }

    public function isNotable(): bool
    {
        return false;
    }

    public function getPreconditions(Setup\Environment $environment): array
    {
        $http_config = $environment->getConfigFor("http");

        return [
            new ilIniFilesPopulatedObjective(),
            new ilDatabaseInitializedObjective(),
            new ilSettingsFactoryExistsObjective(),
            new ilHttpConfigStoredObjective($http_config)
        ];
    }

    public function achieve(Setup\Environment $environment): Setup\Environment
    {
        $client_ini = $environment->getResource(Setup\Environment::RESOURCE_CLIENT_INI);
        $admin_interaction = $environment->getResource(Setup\Environment::RESOURCE_ADMIN_INTERACTION);
        $factory = $environment->getResource(Setup\Environment::RESOURCE_SETTINGS_FACTORY);
        $settings = $factory->settingsFor("common");

        $session_max_idle = $this->config->getSessionMaxIdle();

        //        $apache_php_ini_path = $this->getPHPIniPathForApache("localhost/info.php");
        //        $apache_php_ini = parse_ini_file($apache_php_ini_path);
        //
        //        $cookie_lifetime = $apache_php_ini["session.cookie_lifetime"];
        //        $gc_maxlifetime = $apache_php_ini["session.gc_maxlifetime"];

        $key = bin2hex(random_bytes(32));
        $this->generateServerInfoFile($key);
        var_dump($this->getPHPIniValues($settings, $key));
        die();
        list($cookie_lifetime, $gc_maxlifetime) = $this->getPHPIniValues();

        if ($cookie_lifetime != 0) {
            $message =
                "The value 'session.cookie_lifetime' in your php.ini does not correspond" . PHP_EOL .
                "to the value '0' recommended by ILIAS. Do you want to continue anyway?";

            if (!$admin_interaction->confirmOrDeny($message)) {
                throw new Setup\NoConfirmationException($message);
            }
        }

        if ($gc_maxlifetime <= $session_max_idle) {
            $message =
                "The value 'session.gc_maxlifetime' in your php.ini is smaller or equal than" . PHP_EOL .
                "'session_max_idle' in your ILIAS-Config. ILIAS recommends a bigger value." . PHP_EOL .
                "Do you want to continue anyway?";

            if (!$admin_interaction->confirmOrDeny($message)) {
                throw new Setup\NoConfirmationException($message);
            }
        }

        $client_ini->setVariable("session", "expire", (string) $session_max_idle);

        return $environment;
    }

    public function isApplicable(Setup\Environment $environment): bool
    {
        return true;
    }

    protected function getPHPIniPathForApache(string $url): string
    {
        return exec("wget -q -O - {$url} | grep 'Loaded Configuration File' | cut -d '<' -f5 | cut -d '>' -f2");
    }

    protected function getPHPIniValues(ilSetting $settings, string $key): array
    {
        $curl = new ilCurlConnection(
            "http://doil/trunk10/server_info.php?token=$key",
            new ilProxySettings($settings)
        );
        $curl->init();
        //        try {
        //            $curl = new ilCurlConnection("http://doil/trunk10/server_info.php?token=$key");
        //            $curl->init();
        //
        ////            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        ////            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        ////            $curl->setOpt(CURLOPT_RETURNTRANSFER, 1);
        ////
        ////            $curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        ////            $curl->setOpt(CURLOPT_MAXREDIRS, 3);
        //        } catch (Exception $e) {
        //            var_dump($e);
        //        }

        //        $curl->exec();
        //        var_dump($curl->getResponseBody());
        return  explode(",", $foo);
    }

    protected function generateServerInfoFile(string $key): void
    {
        $content = <<<TEXT
<?php

if (!isset(\$_GET['token'])) {
    return [];
}

if (\$_GET['token'] !== $key) {
    return [];
}

echo ini_get('session.cookie_lifetime'), ini_get('session.gc_maxlifetime');


TEXT;

        file_put_contents("public/server_info.php", $content);
    }
}
