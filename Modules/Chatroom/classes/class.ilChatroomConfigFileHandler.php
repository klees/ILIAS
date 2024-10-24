<?php declare(strict_types=1);
/* Copyright (c) 1998-2016 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilChatroomConfigFileHandler
 * @package Modules\Chatroom\classes
 * @author  Thomas Joußen <tjoussen@databay.de>
 * @since   19.02.16
 * @version $Id$
 */
class ilChatroomConfigFileHandler
{
    private const CHATROOM_DATA_DIR = '/chatroom/';
    private const CHATROOM_CLIENT_CONFIG_FILENAME = 'client.cfg';
    private const CHATROOM_SERVER_CONFIG_FILENAME = 'server.cfg';

    /**
     * Creates a client config file and saves it to the chatroom data directory
     * @param array $settings
     * @throws Exception
     */
    public function createClientConfigFile(array $settings) : void
    {
        $content = $this->getClientFileContent($settings);
        $this->writeDataToFile($content, self::CHATROOM_CLIENT_CONFIG_FILENAME);
    }

    /**
     * Get the client config file content as json encoded string
     * @param array $settings
     * @return string
     */
    protected function getClientFileContent(array $settings) : string
    {
        global $DIC;

        // Dirty configuration swap: Ilias differentiates between InnoDB and MyISAM.
        // MyISAM is configured as mysql, InnoDB as innodb.
        // The client config file only needs information about driver not engine
        $type = $DIC['ilClientIniFile']->readVariable('db', 'type');
        if (in_array($type, [
            ilDBConstants::TYPE_MYSQL,
            ilDBConstants::TYPE_INNODB,
            ilDBConstants::TYPE_PDO_MYSQL_INNODB,
            ilDBConstants::TYPE_PDO_MYSQL_MYISAM,
            ''
        ], true)) {
            $type = 'mysql';
        }

        $settings['database'] = [
            'type' => $type,
            'host' => $DIC['ilClientIniFile']->readVariable('db', 'host'),
            'port' => (int) $DIC['ilClientIniFile']->readVariable('db', 'port'),
            'name' => $DIC['ilClientIniFile']->readVariable('db', 'name'),
            'user' => $DIC['ilClientIniFile']->readVariable('db', 'user'),
            'pass' => $DIC['ilClientIniFile']->readVariable('db', 'pass')
        ];

        return json_encode($settings, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * Writes $content to file named by $filename
     * @param string $content
     * @param string $filename
     * @throws Exception
     */
    protected function writeDataToFile(string $content, string $filename) : void
    {
        $path = $this->createDataDirIfNotExists();
        $handle = fopen($path . $filename, 'wb');

        if (!fwrite($handle, $content)) {
            throw new Exception('Cannot write to file');
        }

        fclose($handle);
    }

    /**
     * Creates a data directory for configuration files, if the directory does not already exists.
     * @return string
     * @throws Exception Throws Exception if data dir creation failed
     */
    protected function createDataDirIfNotExists() : string
    {
        $path = ilFileUtils::getDataDir() . self::CHATROOM_DATA_DIR;

        if (!is_dir($path) && !ilFileUtils::makeDir($path)) {
            throw new Exception('Directory cannot be created');
        }

        return $path;
    }

    /**
     * Creates a server config file and saves it to the chatroom data directory
     * @param array $settings
     * @throws Exception
     */
    public function createServerConfigFile(array $settings) : void
    {
        $content = $this->getServerFileContent($settings);
        $this->writeDataToFile($content, self::CHATROOM_SERVER_CONFIG_FILENAME);
    }

    /**
     * Get the server config file contetn as json encoded string
     * @param array $settings
     * @return string
     */
    protected function getServerFileContent(array $settings) : string
    {
        unset($settings['ilias_proxy'], $settings['client_proxy'], $settings['ilias_url'], $settings['client_url']);

        return json_encode($settings, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
