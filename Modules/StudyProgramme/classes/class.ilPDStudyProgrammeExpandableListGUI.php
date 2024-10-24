<?php declare(strict_types=1);

/* Copyright (c) 2015 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

/**
 * Personal Desktop-Presentation for the Study Programme
 *
 * @ilCtrl_IsCalledBy ilPDStudyProgrammeExpandableListGUI: ilColumnGUI
 */
class ilPDStudyProgrammeExpandableListGUI extends ilPDStudyProgrammeSimpleListGUI
{
    const BLOCK_TYPE = "prgexpandablelist";

    public function __construct()
    {
        parent::__construct();
    }

    protected function shouldShowThisList() : bool
    {
        $cmd = $this->request_wrapper->retrieve("cmd", $this->refinery->kindlyTo()->string());
        $expand = $this->request_wrapper->retrieve("expand", $this->refinery->kindlyTo()->bool());
        return $cmd == "jumpToSelectedItems" && $expand;
    }

    protected function new_ilStudyProgrammeAssignmentListGUI(
        ilStudyProgrammeAssignment $assignment
    ) : ilStudyProgrammeExpandableProgressListGUI {
        $prg = ilObjStudyProgramme::getInstanceByObjId($assignment->getRootId());
        $progress = $prg->getProgressForAssignment($assignment->getId());
        $progress_gui = new ilStudyProgrammeExpandableProgressListGUI($progress);
        $progress_gui->setOnlyRelevant(true);
        return $progress_gui;
    }
}
