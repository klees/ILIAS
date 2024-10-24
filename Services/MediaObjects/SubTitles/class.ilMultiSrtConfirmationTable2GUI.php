<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

/**
 * List srt files from zip file for upload confirmation
 *
 * @author Alexander Killing <killing@leifos.de>
 */
class ilMultiSrtConfirmationTable2GUI extends ilTable2GUI
{
    protected ilAccessHandler $access;
    protected ilObjUser $user;
    protected ilObjMediaObject $mob;

    public function __construct(
        object $a_parent_obj,
        string $a_parent_cmd
    ) {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->lng = $DIC->language();
        $this->access = $DIC->access();
        $this->user = $DIC->user();
        $ilCtrl = $DIC->ctrl();
        $lng = $DIC->language();

        $this->mob = $a_parent_obj->object;
        $lng->loadLanguageModule("meta");

        $this->setId("mob_msrt_upload");
        parent::__construct($a_parent_obj, $a_parent_cmd);
        $this->setLimit(9999);
        $this->setData($this->mob->getMultiSrtFiles());
        $this->setTitle($lng->txt("mob_multi_srt_files"));
        $this->setSelectAllCheckbox("file");

        $this->addColumn("", "", "1px", true);
        $this->addColumn($this->lng->txt("filename"), "filename");
        $this->addColumn($this->lng->txt("language"), "language");

        $this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
        $this->setRowTemplate("tpl.multi_srt_confirmation_row.html", "Services/MediaObjects");

        $this->addCommandButton("saveMultiSrt", $lng->txt("save"));
        $this->addCommandButton("cancelMultiSrt", $lng->txt("cancel"));
    }

    protected function fillRow(array $a_set) : void
    {
        $lng = $this->lng;

        if ($a_set["lang"] != "") {
            $this->tpl->setCurrentBlock("cb");
            $language = $lng->txt("meta_l_" . $a_set["lang"]);
            $this->tpl->setVariable("LANGUAGE", $language);
            $this->tpl->setVariable("POST_FILE", ilLegacyFormElementsUtil::prepareFormOutput($a_set["filename"]));
            $this->tpl->parseCurrentBlock();
        }
        $this->tpl->setVariable("FILENAME", $a_set["filename"]);
    }
}
