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
* GUI class for SCORM Manifest element
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
*
* @ingroup ModulesScormAicc
*/
class ilSCORMmanifestGUI extends ilSCORMObjectGUI
{
    /**
     * @param int $a_id
     */
    public function __construct(int $a_id)
    {
        parent::__construct();
        $this->sc_object = new ilSCORMManifest($a_id);
    }

    /**
     * @return void
     */
    public function view() : void
    {
        $this->tpl->addBlockFile("CONTENT", "content", "tpl.scorm_obj.html", "Modules/ScormAicc");
        $this->displayParameter(
            $this->lng->txt("cont_import_id"),
            $this->sc_object->getImportId()
        );
        $this->displayParameter(
            $this->lng->txt("cont_version"),
            $this->sc_object->getVersion()
        );
        $this->displayParameter(
            $this->lng->txt("cont_xml_base"),
            $this->sc_object->getXmlBase()
        );
        $this->tpl->setCurrentBlock("par_table");
        $this->tpl->setVariable("TXT_OBJECT_TYPE", $this->lng->txt("cont_manifest"));
        $this->tpl->parseCurrentBlock();
    }
}
