<?php declare(strict_types=1);
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
    |                                                                             |
    | This program is free software; you can redistribute it and/or               |
    | modify it under the terms of the GNU General Public License                 |
    | as published by the Free Software Foundation; either version 2              |
    | of the License, or (at your option) any later version.                      |
    |                                                                             |
    | This program is distributed in the hope that it will be useful,             |
    | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
    | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
    | GNU General Public License for more details.                                |
    |                                                                             |
    | You should have received a copy of the GNU General Public License           |
    | along with this program; if not, write to the Free Software                 |
    | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
    +-----------------------------------------------------------------------------+
*/

/**
 * @author  Stefan Meyer <meyer@leifos.com>
 * @ingroup ServicesAdvancedMetaData
 * @todo    use logger and filestorage
 */
class ilAdvancedMDRecordExportFiles
{
    protected string $export_dir = '';

    public function __construct(int $a_obj_id = null)
    {
        $this->export_dir = ilFileUtils::getDataDir() . '/ilAdvancedMetaData/export';
        if ($a_obj_id) {
            $this->export_dir .= "_" . $a_obj_id;
        }
        $this->init();
    }

    /**
     * Read files info
     * @access public
     * @return array array e.g array(records => 'ECS-Server',size => '123',created' => 121212)
     */
    public function readFilesInfo() : array
    {
        $file_info = array();
        foreach ($this->getFiles() as $name => $data) {
            if ($data['type'] != 'file') {
                continue;
            }
            $file_parts = explode('.', $name);
            if (!is_numeric($file_parts[0]) or (strcmp('xml', $file_parts[1]) != 0)) {
                continue;
            }
            $file_info = [];
            $file_info[$file_parts[0]]['size'] = $data['size'];
            $file_info[$file_parts[0]]['date'] = $file_parts[0];

            if ($xml = simplexml_load_file($this->export_dir . '/' . $name)) {
                $records = array();
                foreach ($xml->xpath('Record/Title') as $title) {
                    $records[] = (string) $title;
                }
                $file_info[$file_parts[0]]['name'] = $records;
            }
        }
        return $file_info;
    }

    /**
     * Get files
     * @return array<int, array{type: string, size: string}>
     */
    public function getFiles() : array
    {
        if (!@is_dir($this->export_dir)) {
            return array();
        }
        $files = [];
        foreach (ilFileUtils::getDir($this->export_dir) as $file_name => $file_data) {
            $files[$file_name] = $file_data;
        }
        return $files;
    }

    /**
     * Create new export file from xml string
     */
    public function create(string $a_xml) : void
    {
        global $DIC;

        $ilLog = $DIC['ilLog'];

        if (!$fp = @fopen($this->export_dir . '/' . time() . '.xml', 'w+')) {
            $ilLog->write(__METHOD__ . ': Cannot open file ' . $this->export_dir . '/' . time() . '.xml');
            throw new ilException('Cannot write export file.');
        }

        @fwrite($fp, $a_xml);
        @fclose($fp);
    }

    public function deleteByFileId(int $a_timest) : bool
    {
        global $DIC;

        $ilLog = $DIC['ilLog'];

        if (!unlink($this->export_dir . '/' . $a_timest . '.xml')) {
            $ilLog->write(__METHOD__ . ': Cannot delete file ' . $this->export_dir . '/' . $a_timest . '.xml');
            return false;
        }
        return true;
    }

    public function getAbsolutePathByFileId(int $a_file_basename) : string
    {
        global $DIC;

        $ilLog = $DIC['ilLog'];

        $a_file_basename = (string) $a_file_basename;
        if (!@file_exists($this->export_dir . '/' . $a_file_basename . '.xml')) {
            $ilLog->write(__METHOD__ . ': Cannot find file ' . $this->export_dir . '/' . $a_file_basename . '.xml');
            return '';
        }
        return $this->export_dir . '/' . $a_file_basename . '.xml';
    }

    /**
     * init export directory
     * @access private
     */
    private function init() : void
    {
        if (!@is_dir($this->export_dir)) {
            ilFileUtils::makeDirParents($this->export_dir);
        }
    }
}
