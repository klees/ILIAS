<?php declare(strict_types=1);
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
/**
* GUI class for SCORM Organization element
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
*
* @ingroup ModulesScormAicc
*/
class ilSCORMOrganizationGUI extends ilSCORMObjectGUI
{
    /**
     * @param int $a_id
     */
    public function __construct(int $a_id)
    {
        parent::__construct();
        $this->sc_object = new ilSCORMOrganization($a_id);
    }

    /**
     * @return void
     */
    public function view() : void
    {
        $this->tpl->addBlockFile("CONTENT", "content", "tpl.scorm_obj.html", "Modules/ScormAicc");
        $this->tpl->setCurrentBlock("par_table");
        $this->tpl->setVariable("TXT_OBJECT_TYPE", $this->lng->txt("cont_organization"));
        $this->displayParameter(
            $this->lng->txt("cont_import_id"),
            $this->sc_object->getImportId()
        );
        $this->displayParameter(
            $this->lng->txt("cont_structure"),
            $this->sc_object->getStructure()
        );
        $this->displayParameter(
            $this->lng->txt("cont_sc_title"),
            $this->sc_object->getTitle()
        );
        $this->tpl->parseCurrentBlock();
    }
}
