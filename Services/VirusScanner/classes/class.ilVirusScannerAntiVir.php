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
class ilVirusScannerAntiVir extends ilVirusScanner
{
    public function __construct(string $scan_command, string $clean_command)
    {
        parent::__construct($scan_command, $clean_command);
        $this->type = "antivir";
        $this->scanZipFiles = true;
    }

    public function scanFile(string $file_path, string $org_name = "") : string
    {
        $this->scanFilePath = $file_path;
        $this->scanFileOrigName = $org_name;

        // Call of antivir command
        $cmd = $this->scanCommand . " " . $file_path . " ";
        exec($cmd, $out, $ret);
        $this->scanResult = implode("\n", $out);

        // sophie could be called
        if (preg_match('/ALERT:/', $this->scanResult)) {
            $this->scanFileIsInfected = true;
            $this->logScanResult();
            return $this->scanResult;
        } else {
            $this->scanFileIsInfected = false;
            return "";
        }
    }
}
