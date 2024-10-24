<?php declare(strict_types=0);
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
 * @defgroup
 * @author Stefan Meyer <meyer@leifos.com>
 * @ingroup
 */
class ilCourseInfoFileTableGUI extends ilTable2GUI
{
    public function __construct(object $a_parent_obj, string $a_parent_cmd = '')
    {

        parent::__construct($a_parent_obj, $a_parent_cmd);
        $this->addColumn('', 'f', '1');
        $this->addColumn($this->lng->txt('filename'), 'filename', "60%");
        $this->addColumn($this->lng->txt('filesize'), 'filesize', "20%");
        $this->addColumn($this->lng->txt('filetype'), 'filetype', "20%");

        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
        $this->setRowTemplate("tpl.crs_info_file_row.html", "Modules/Course");
        $this->setDefaultOrderField("filename");
        $this->setDefaultOrderDirection("desc");
    }

    public function numericOrdering(string $a_field) : bool
    {
        switch ($a_field) {
            case 'filesize':
                return true;
        }
        return parent::numericOrdering($a_field);
    }

    protected function fillRow(array $a_set) : void
    {
        $this->tpl->setVariable('VAL_ID', $a_set['id']);
        $this->tpl->setVariable('VAL_FILENAME', $a_set['filename']);
        $this->tpl->setVariable('VAL_FILETYPE', $a_set['filetype']);
        $this->tpl->setVariable('VAL_FILESIZE', $a_set['filesize']);
    }
}
