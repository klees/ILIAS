<?php

/* Copyright (c) 1998-2021 ILIAS open source, GPLv3, see LICENSE */

use ILIAS\Exercise\InternalService;
use ILIAS\Exercise\Assignment\Mandatory;

/**
 * Exercise participant table
 *
 * @author Alexander Killing <killing@leifos.de>
 */
class ilExGradesTableGUI extends ilTable2GUI
{
    protected InternalService $service;
    protected Mandatory\RandomAssignmentsManager $random_ass_manager;
    protected ?ilObjExercise $exc;
    protected int $exc_id;
    protected ilExerciseMembers $mem_obj;
    /**
     * @var ilExAssignment[]
     */
    protected array $ass_data;

    /**
     * @throws ilExcUnknownAssignmentTypeException
     */
    public function __construct(
        object $a_parent_obj,
        string $a_parent_cmd,
        InternalService $service,
        ilExerciseMembers $a_mem_obj
    ) {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->lng = $DIC->language();
        $ilCtrl = $DIC->ctrl();
        $lng = $DIC->language();
        $request = $DIC->exercise()->internal()->gui()->request();
        
        $this->exc = $request->getExercise();
        $this->service = $service;
        $this->random_ass_manager = $service->domain()->assignment()->randomAssignments($this->exc);

        $this->exc_id = $this->exc->getId();
        
        $this->setId("exc_grades_" . $this->exc_id);
        
        $this->mem_obj = $a_mem_obj;
        
        $mems = $this->mem_obj->getMembers();
        $mems = $GLOBALS['DIC']->access()->filterUserIdsByRbacOrPositionOfCurrentUser(
            'edit_submissions_grades',
            'edit_submissions_grades',
            $this->exc->getRefId(),
            $mems
        );
        
        $data = array();
        foreach ($mems as $d) {
            $data[$d] = ilObjUser::_lookupName($d);
            $data[$d]["user_id"] = $d;
            $data[$d]["name"] = $data[$d]["lastname"] . ", " . $data[$d]["firstname"];
        }
        
        parent::__construct($a_parent_obj, $a_parent_cmd);
        $this->setData($data);
        $this->ass_data = ilExAssignment::getInstancesByExercise($this->exc_id);
        
        //var_dump($data);
        $this->setTitle($lng->txt("exc_grades"));
        $this->setTopCommands(true);
        //$this->setLimit(9999);
        
        //		$this->addColumn("", "", "1", true);
        $this->addColumn($this->lng->txt("name"), "name");
        $cnt = 1;
        foreach ($this->ass_data as $ass) {
            $ilCtrl->setParameter($this->parent_obj, "ass_id", $ass->getId());
            $cnt_str = '<a href="' . $ilCtrl->getLinkTarget($this->parent_obj, "members") . '">' . $cnt . '</a>';
            if (!$this->random_ass_manager->isActivated() && $ass->getMandatory()) {
                $this->addColumn("<u>" . $cnt_str . "</u>", "", "", false, "", $ass->getTitle() . " " .
                    "(" . $lng->txt("exc_mandatory") . ")");
            } else {
                $this->addColumn($cnt_str, "", "", false, "", $ass->getTitle());
            }
            $cnt++;
        }
        $ilCtrl->setParameter($this->parent_obj, "ass_id", "");
        
        $this->addColumn($this->lng->txt("exc_total_exc"), "");
        $this->lng->loadLanguageModule("trac");
        $this->addColumn($this->lng->txt("trac_comment"));
        
        //		$this->addColumn($this->lng->txt("exc_grading"), "solved_time");
        //		$this->addColumn($this->lng->txt("mail"), "feedback_time");
        
        $this->setDefaultOrderField("name");
        $this->setDefaultOrderDirection("asc");
        
        $this->setEnableHeader(true);
        $this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
        $this->setRowTemplate("tpl.exc_grades_row.html", "Modules/Exercise");
        //$this->disable("footer");
        $this->setEnableTitle(true);
        //		$this->setSelectAllCheckbox("assid");

        if (count($mems) > 0) {
            $this->addCommandButton("saveGrades", $lng->txt("exc_save_changes"));
        }
    }
    
    public function numericOrdering(string $a_field) : bool
    {
        if (in_array($a_field, array("order_val"))) {
            return true;
        }
        return false;
    }
    
    protected function fillRow(array $a_set) : void
    {
        $lng = $this->lng;
        $ilCtrl = $this->ctrl;

        $user_id = $a_set["user_id"];
        
        foreach ($this->ass_data as $ass) {
            $member_status = new ilExAssignmentMemberStatus($ass->getId(), $user_id);
            
            // grade
            $this->tpl->setCurrentBlock("grade");
            $status = $member_status->getStatus();
            $this->tpl->setVariable("SEL_" . strtoupper($status), ' selected="selected" ');
            $this->tpl->setVariable("TXT_NOTGRADED", $lng->txt("exc_notgraded"));
            $this->tpl->setVariable("TXT_PASSED", $lng->txt("exc_passed"));
            $this->tpl->setVariable("TXT_FAILED", $lng->txt("exc_failed"));
            $pic = $member_status->getStatusIcon();
            $this->tpl->setVariable("IMG_STATUS", ilUtil::getImagePath($pic));
            $this->tpl->setVariable("ALT_STATUS", $lng->txt("exc_" . $status));
            
            // mark
            $mark = $member_status->getMark();
            $this->tpl->setVariable("VAL_ONLY_MARK", $mark);
            
            $this->tpl->parseCurrentBlock();
        }
        
        // exercise total
        
        // mark input
        $this->tpl->setCurrentBlock("mark_input");
        $this->tpl->setVariable("TXT_MARK", $lng->txt("exc_mark"));
        $this->tpl->setVariable(
            "NAME_MARK",
            "mark[" . $user_id . "]"
        );
        $mark = ilLPMarks::_lookupMark($user_id, $this->exc_id);
        $this->tpl->setVariable(
            "VAL_MARK",
            ilLegacyFormElementsUtil::prepareFormOutput($mark)
        );
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock("grade");
        $status = ilExerciseMembers::_lookupStatus($this->exc_id, $user_id);
        $this->tpl->setVariable("SEL_" . strtoupper($status), ' selected="selected" ');
        switch ($status) {
            case "passed": 	$pic = "scorm/passed.svg"; break;
            case "failed":	$pic = "scorm/failed.svg"; break;
            default: 		$pic = "scorm/not_attempted.svg"; break;
        }
        $this->tpl->setVariable("IMG_STATUS", ilUtil::getImagePath($pic));
        $this->tpl->setVariable("ALT_STATUS", $lng->txt("exc_" . $status));
        
        // mark
        /*$this->tpl->setVariable("TXT_MARK", $lng->txt("exc_mark"));
        $this->tpl->setVariable("NAME_MARK",
            "mark[".$d["id"]."]");
        $mark = ilExAssignment::lookupMarkOfUser($ass["id"], $user_id);
        $this->tpl->setVariable("VAL_MARK",
            ilUtil::prepareFormOutput($mark));*/
        
        $this->tpl->parseCurrentBlock();

        // name
        $this->tpl->setVariable(
            "TXT_NAME",
            $a_set["lastname"] . ", " . $a_set["firstname"] . " [" . $a_set["login"] . "]"
        );
        $this->tpl->setVariable("VAL_ID", $user_id);
        
        // #17679
        $ilCtrl->setParameter($this->parent_obj, "part_id", $user_id);
        $url = $ilCtrl->getLinkTarget($this->parent_obj, "showParticipant");
        $ilCtrl->setParameter($this->parent_obj, "part_id", "");
        
        $this->tpl->setVariable("LINK_NAME", $url);
        
        // comment
        $this->tpl->setVariable("ID_COMMENT", $user_id);
        $c = ilLPMarks::_lookupComment($user_id, $this->exc_id);
        $this->tpl->setVariable(
            "VAL_COMMENT",
            ilLegacyFormElementsUtil::prepareFormOutput($c)
        );
    }
}
