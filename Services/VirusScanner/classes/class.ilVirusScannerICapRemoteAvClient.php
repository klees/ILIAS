<?php
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

class ilVirusScannerICapRemoteAvClient extends ilVirusScannerICapRemote
{
    const HEADER = 'headers';
    const HEADER_VIOLATION_FOUND = 'X-Violations-Found';
    const HEADER_INFECTION_FOUND = 'X-Infection-Found';

    public function __construct(string $scan_command, string $clean_command)
    {
        parent::__construct($scan_command, $clean_command);
        $this->options(IL_ICAP_AV_COMMAND);
    }

    public function scanFile(string $file_path, string $org_name = "") : string
    {
        $return_string = '';
        if (is_readable($file_path)) {
            $results = ($this->reqmod(
                'avscan',
                [
                    'req-hdr' => "POST /test HTTP/1.1\r\nHost: 127.0.0.1\r\n\r\n",
                    'req-body' => file_get_contents($file_path) //Todo: find a better way
                ]
            ));
            if ($this->analyseHeader($results)) {
                $return_string = sprintf('Virus found in file "%s"!', $file_path);
                $this->log->warning($return_string);
            }
        } else {
            $return_string = sprintf('File "%s" not found or not readable.', $file_path);
            $this->log->warning($return_string);
        }
        $this->log->info(sprintf('No virus found in file "%s".', $file_path));
        return $return_string;
    }

    protected function analyseHeader(array $header) : bool
    {
        $virus_found = false;
        if (array_key_exists(self::HEADER, $header)) {
            $header = $header[self::HEADER];
            if (array_key_exists(self::HEADER_VIOLATION_FOUND, $header)) {
                if ($header[self::HEADER_VIOLATION_FOUND] > 0) {
                    $virus_found = true;
                }
            }
            if (array_key_exists(self::HEADER_INFECTION_FOUND, $header)) {
                if (strlen($header[self::HEADER_INFECTION_FOUND]) > 0) {
                    $infection_split = preg_split('/;/', $header[self::HEADER_INFECTION_FOUND]);
                    foreach ($infection_split as $infection) {
                        $parts = preg_split('/=/', $infection);
                        if ($parts !== false &&
                            is_array($parts) &&
                            count($parts) > 0 &&
                            strlen($parts[0]) > 0) {
                            $this->log->warning(trim($parts[0]) . ': ' . trim($parts[1]));
                        }
                    }
                    $virus_found = true;
                }
            }
        }
        return $virus_found;
    }
}
