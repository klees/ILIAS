<?php declare(strict_types=1);

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
 ********************************************************************
 */

/**
* @author Stefan Meyer <meyer@leifos.com>
* @version $Id$
*
* @ingroup ModulesCourse
*/
class ilSessionFileTableGUI extends ilTable2GUI
{
    public function __construct(object $a_parent_obj, string $a_parent_cmd = '')
    {
        parent::__construct($a_parent_obj, $a_parent_cmd);
        $this->addColumn('', 'f', "1");
        $this->addColumn($this->lng->txt('filename'), 'filename', "60%");
        $this->addColumn($this->lng->txt('filesize'), 'filesize', "20%");
        $this->addColumn($this->lng->txt('filetype'), 'filetype', "20%");
        
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
        $this->setRowTemplate("tpl.sess_file_row.html", "Modules/Session");
        $this->setDefaultOrderField("filname");
        $this->setDefaultOrderDirection("desc");
    }

    protected function fillRow(array $a_set) : void
    {
        $this->tpl->setVariable('VAL_ID', $a_set['id']);
        $this->tpl->setVariable('VAL_FILENAME', $a_set['filename']);
        $this->tpl->setVariable('VAL_FILETYPE', $a_set['filetype']);
        $this->tpl->setVariable('VAL_FILESIZE', $a_set['filesize']);
    }
}
