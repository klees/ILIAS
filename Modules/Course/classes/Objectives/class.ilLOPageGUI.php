<?php declare(strict_types=0);

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * (Course) learning objective page GUI class
 * @author       Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @ilCtrl_Calls ilLOPageGUI: ilPageEditorGUI, ilEditClipboardGUI, ilMDEditorGUI
 * @ilCtrl_Calls ilLOPageGUI: ilPublicUserProfileGUI, ilNoteGUI
 * @ilCtrl_Calls ilLOPageGUI: ilPropertyFormGUI, ilInternalLinkGUI, ilPageMultiLangGUI
 * @ingroup      ModulesCourse
 */
class ilLOPageGUI extends ilPageObjectGUI
{
    public function __construct(int $a_id = 0, int $a_old_nr = 0, string $a_lang = "")
    {
        parent::__construct("lobj", $a_id, $a_old_nr, false, $a_lang);
    }
}
