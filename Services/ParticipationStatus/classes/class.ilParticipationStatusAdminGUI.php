<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once "./Services/ParticipationStatus/classes/class.ilParticipationStatus.php";
require_once "./Services/ParticipationStatus/classes/class.ilParticipationStatusPermissions.php";
require_once "./Services/ParticipationStatus/classes/class.ilParticipationStatusHelper.php";

/**
 * Participation status GUI 
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @ingroup ServicesParticipationStatus
 * @ilCtrl_Calls ilParticipationStatusAdminGUI: 
 */
class ilParticipationStatusAdminGUI
{	
	protected $course; // [ilObjCourse]
	protected $permissions; // [ilParticipationStatusPermissions]
	protected $pstatus; // [ilParticipationStatus]
	
	// gev-patch start
	public $from_foreign_class = false;
	public $crs_ref_id = false;
	// gev-patch end

	/**
	 * Constructor
	 * 
	 * @param ilObjCourse $a_course
	 * @return self
	 */
	// gev-patch start
	//public function __construct(ilObjCourse $a_course)
	public function __construct(ilObjCourse $a_course, $from_foreign_class=false)
	// gev-patch end
	{
		global $lng;
		
		$this->setCourse($a_course);	
		
		$perm = ilParticipationStatusPermissions::getInstance($this->getCourse());
		$this->setPermissions($perm);
		
		if(!$this->getPermissions()->viewParticipationStatus() &&
			!$this->getPermissions()->setParticipationStatus() &&
			!$this->getPermissions()->reviewParticipationStatus())
		{
			// gev-patch start
			//ilUtil::sendFailure($lng->txt("msg_no_perm_read"), true);
			//$this->returnToParent();
			if(!$from_foreign_class){
				ilUtil::sendFailure($lng->txt("msg_no_perm_read"), true);
				$this->returnToParent();
			}
			// gev-patch end
		}
		
		$lng->loadLanguageModule("ptst");

		$this->setParticipationStatus(ilParticipationStatus::getInstance($this->getCourse()));

		//gev patch start
		$this->gLng = $lng;
		//gev patch end
	}
	
	/**
	 * Factory
	 * 
	 * @throws ilException
	 * @param int $a_ref_id
	 * @return self
	 */
	// gev-patch start
	//public static function getInstanceByRefId($a_ref_id)
	public static function getInstanceByRefId($a_ref_id, $from_foreign_class=false)
	// gev-patch end
	{
		global $tree;
		
		if(ilObject::_lookupType($a_ref_id, true) != "crs" ||
			$tree->isDeleted($a_ref_id))
		{
			// gev-patch start
			//throw new ilException("ilParticipationStatusAdminGUI - needs course ref id");
			if($from_foreign_class){
				return false;
			}else{
				throw new ilException("ilParticipationStatusAdminGUI - needs course ref id");
			}
			// gev-patch end
		}
		
		require_once "Modules/Course/classes/class.ilObjCourse.php";
		$course = new ilObjCourse($a_ref_id);
		
		// gev-patch start
		//return new self($course);
		return new self($course, $from_foreign_class);
		// gev-patch end
	}
	
	
	//
	// properties
	//
	
	/**
	 * Set course
	 * 
	 * @param ilObjCourse $a_course
	 */
	protected function setCourse(ilObjCourse $a_course)
	{
		$this->course = $a_course;
	}
	
	/**
	 * Get course
	 * 
	 * @return ilObjCourse 
	 */	
	// gev-patch start
	//protected function getCourse()
	// gev-patch end
	public function getCourse()
	{
		return $this->course;
	}
	
	/**
	 * Set permissions
	 * 
	 * @param ilParticipationStatusPermissions $a_perms
	 */
	protected function setPermissions(ilParticipationStatusPermissions $a_perms)
	{
		$this->permissions = $a_perms;
	}
	
	/**
	 * Get permissions
	 * 
	 * @return ilParticipationStatusPermissions 
	 */	
	protected function getPermissions()
	{
		return $this->permissions;
	}
	
	/**
	 * Set participation status
	 * 
	 * @param ilParticipationStatus $a_status
	 */
	protected function setParticipationStatus(ilParticipationStatus $a_status)
	{
		$this->pstatus = $a_status;
	}
	
	/**
	 * Get participation status
	 * 
	 * @return ilParticipationStatus 
	 */	
	// gev-patch start
	//protected function getParticipationStatus()
	public function getParticipationStatus()
	// gev-patch end
	
	{
		return $this->pstatus;
	}
	
			
	//
	// GUI basics
	//
	
	/**
	 * Execute request command
	 * 
	 * @return boolean
	 */
	public function executeCommand()
	{
		global $ilCtrl, $lng, $ilTabs;
				
		// nothing can be done before certain date is reached
		$helper = ilParticipationStatusHelper::getInstance($this->getCourse());
		if(!$helper->isStartForParticipationStatusSettingReached())
		{

			// gev-patch start
			/*
			$this->setTabs("listStatus");
			*/
			// gev-patch end
			
			ilDatePresentation::setUseRelativeDates(false);
			$start = $helper->getStartForParticipationStatusSetting();
			if ($start !== null) {
				ilUtil::sendInfo(sprintf($lng->txt("ptst_admin_start_date_not_reached"), 
					ilDatePresentation::formatDate($start)));
			}
			
			// gev-patch start
			if($this->from_foreign_class) {
				$trgt = "listStatus&crsrefid=" .$this->crs_ref_id;
				$ilTabs->clearTargets();
				$ilTabs->setBackTarget($lng->txt("back"),
					$ilCtrl->getLinkTarget($this, $trgt));
			}else{
				$this->setTabs("listStatus");
			}
			// gev-patch end
		
		}
		else
		{
			// gev-patch start
			/*
			$next_class = $ilCtrl->getNextClass($this);
			$cmd = $ilCtrl->getCmd("listStatus");

			switch($next_class)
			{						
				default:		
					$this->$cmd();
					break;
			}
			*/
			$cmd = $ilCtrl->getCmd("listStatus");
			$this->$cmd();
			// gev-patch end
		}
		
		return true;
	}
	
	/**
	 * Set tabs
	 * 
	 * @param string $a_active
	 */
	protected function setTabs($a_active)
	{
		global $ilTabs, $ilCtrl, $lng;
		
		$ilTabs->clearTargets();
		
		/*
		$ilTabs->setBackTarget($lng->txt("back"),
			$ilCtrl->getLinkTargetByClass("ilobjcoursegui", "members"));
		*/
		$ilTabs->setBackTarget($lng->txt("back"),
			$ilCtrl->getLinkTarget($this, "returnToParent"));
		
		$ilTabs->addTab("listStatus",
			$lng->txt("ptst_admin_tab_list_status"),
			$ilCtrl->getLinkTarget($this, "listStatus"));
		
		$ilTabs->activateTab($a_active);
	}
	
	/**
	 * Return to parent GUI
	 */
	protected function returnToParent()
	{
		global $ilCtrl;
		
		$ilCtrl->redirectByClass(array("ilRepositoryGUI", "ilObjCourseGUI"), "members");		
		// $ilCtrl->returnToParent($this);
	}
	
	// gev-patch start
	/**
	 * Return to parent list-view, maybe from another class...
	 */
	protected function returnToList()
	{
		global $ilCtrl;
		
		if(! $this->from_foreign_class) {
			//nothing changed here..,
			$ilCtrl->redirect($this, "listStatus");
		} else {
			$ilCtrl->redirect(	$this, 
								"listStatus&crsrefid=" .$this->crs_ref_id
								);
		}
	}
	// gev-patch end



	//
	// STATUS
	// 
	
	/**
	 * Check if current user has write access
	 * 
	 * @return bool	 
	 */
	// gev-patch start
	//protected function mayWrite()
	public function mayWrite()
	// gev-patch end
	{
		$state = $this->getParticipationStatus()->getProcessState();
		return (($state == ilParticipationStatus::STATE_SET &&
				$this->getPermissions()->setParticipationStatus()) ||
			($state == ilParticipationStatus::STATE_REVIEW &&
				$this->getPermissions()->reviewParticipationStatus()));
	}
	
	/**
	 * List course member status and credit points
	 * 
	 * @param array $a_invalid
	 */
	protected function listStatus(array $a_invalid = null)
	{
		global $ilToolbar, $ilCtrl, $lng, $tpl;
		
		$this->setTabs("listStatus");
					
		$may_write = $this->mayWrite();		
		if($this->getParticipationStatus()->getMode() == ilParticipationStatus::MODE_CONTINUOUS)
		{
			$may_finalize = false;
		}
		else
		{
			// gev-patch start
			$may_finalize = $may_write;
			// gev-patch end
		}
		
		// gev-patch start
		self::renderToolbar($this, $this->getParticipationStatus(), $this->getCourse(), $may_write, $may_finalize);
		// gev-patch end
		
		require_once "Services/ParticipationStatus/classes/class.ilParticipationStatusTableGUI.php";
		$tbl = new ilParticipationStatusTableGUI($this, "listStatus", $this->getCourse(), $may_write, $may_finalize, $a_invalid);
		
		//gev patch start
		require_once("Services/GEV/Utils/classes/class.gevCourseUtils.php");
		$crs_utils = gevCourseUtils::getInstanceByObj($this->getCourse());
		$min_parti = ($crs_utils->getMinParticipants() === null) ? 0 : $crs_utils->getMinParticipants();
		$succ_parti = $crs_utils->getSuccessfullParticipants();

		$getSuccessfullParticipants = "";
		if($min_parti > 0 && $min_parti > count($succ_parti)) {
			$tpl_adivce = new ilTemplate("tpl.gev_my_advice.html", true, true, "Services/GEV/Desktop");
			$tpl_adivce->setCurrentBlock("advice");
			$tpl_adivce->setVariable("ADVICE", sprintf($this->gLng->txt("gev_training_min_participation_count_not_reached"),$min_parti));
			$tpl_adivce->parseCurrentBlock();

			$getSuccessfullParticipants = $tpl_adivce->get();
		}
		// gev patch end

		$tpl->setContent($getSuccessfullParticipants.$tbl->getHTML());
	}
	
	// gev-patch start
	static public function renderToolbar($gui, ilParticipationStatus $pstatus, ilObjCourse $course, $may_write, $may_finalize) {
		global $ilToolbar, $ilCtrl, $lng, $tpl;
		
		$helper = ilParticipationStatusHelper::getInstance($course);
		
		if (($helper->getCourseNeedsAttendanceList() || $helper->getCourseNeedsInvitationMailConfirmation()) && $may_write) {
			$ilToolbar->setFormAction($ilCtrl->getFormAction($gui), true);
		}
		
		if($helper->getCourseNeedsAttendanceList())
		{
			if($may_write)
			{
				require_once "Services/Form/classes/class.ilFileInputGUI.php";
				$file = new ilFileInputGUI($lng->txt("ptst_admin_attendance_list"), "atlst");
				$ilToolbar->addInputItem($file, true);
				
				$ilToolbar->addFormButton($lng->txt("upload"), "uploadAttendanceList");

				$ilToolbar->addSeparator();
			}
			if($pstatus->getAttendanceList())
			{
				if($may_write)
				{
					$ilToolbar->addButton($lng->txt("delete"), 
						$ilCtrl->getLinkTarget($gui, "deleteAttendanceList"));

					$ilToolbar->addSeparator();
				}
				
				$ilToolbar->addButton($lng->txt("ptst_admin_view_attendance_list"),
					$ilCtrl->getLinkTarget($gui, "viewAttendanceList"));
			}
			else
			{
				$ilToolbar->addText($lng->txt("ptst_admin_no_attendance_list"));
			}
		}

		if ($helper->getCourseNeedsAttendanceList() && $helper->getCourseNeedsInvitationMailConfirmation()) {
			$ilToolbar->addSeparator();
		}

		if ($helper->getCourseNeedsInvitationMailConfirmation()) {
			require_once("Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php");
			require_once("Services/UIComponent/Toolbar/interfaces/interface.ilToolbarItem.php");
			require_once("Services/Form/classes/class.ilDateTimeInputGUI.php");
			require_once("Services/Form/classes/class.ilCheckboxInputGUI.php");
			
			$mail_send_date = $pstatus->getMailSendDate();
			
			$dt_inp = new ilDateTimeInputGUI("", "mail_send_at");
			$dt_inp->setDisabled(!$may_write);
			if ($mail_send_date) {
				$dt_inp->setDate(new ilDate($mail_send_date,IL_CAL_DATE));
			}
			$ilToolbar->addText($lng->txt("gev_pstatus_mail_date_title"));
			$ilToolbar->addInputItem($dt_inp);
			
			$ilToolbar->addSeparator();
				
			$confirm = new ilCheckboxInputGUI("", "mail_send_confirm");
			$confirm->setDisabled(!$may_write);
			$confirm->setChecked($mail_send_date);
			$ilToolbar->addText($lng->txt("gev_pstatus_mail_confirm_title"));
			$ilToolbar->addInputItem($confirm);
			
			if ($may_write) {
				$ilToolbar->addFormButton($lng->txt("save"), "saveSendMailDate");
			}
		}
	}
	// gev-patch end
	
	
	//
	// TABLE GUI ACTIONS
	// 
	
	/**
	 * Save (list) form data
	 * 
	 * @param bool $a_return
	 */
	protected function saveStatusAndPoints($a_return = false)
	{
		global $ilCtrl, $lng;

		require_once("Services/GEV/Utils/classes/class.gevCourseUtils.php");
		$crs_utils = gevCourseUtils::getInstanceByObj($this->getCourse());

		$status = $_POST["status"];
		$points = $_POST["cpoints"];
		
		if(!$this->mayWrite() ||
			!is_array($status) ||
			!is_array($points))
		{
			
			// gev-patch start
			//$ilCtrl->redirect($this, "listStatus");
			$this->returnToList();
			// gev-patch end
		}
		
		$helper = ilParticipationStatusHelper::getInstance($this->getCourse());
		$max = $helper->getMaxCreditPoints();
		
		$invalid = array();
		
		// currently only invalid points possible
		foreach($points as $user_id => $point)
		{	
			if($point != "" && !is_numeric($point))
			{
				$invalid["points"][] = $user_id;				
				continue;
			}		
			$point = (int)$point;
			if($point < 0 || $point > $max)
			{
				$invalid["points"][] = $user_id;		
			}			
		}
		
		if(sizeof($invalid))
		{
			ilUtil::sendFailure($lng->txt("form_input_not_valid"));
			if(!$a_return)
			{
				return $this->listStatus($invalid);
			}
			else
			{
				return $invalid;
			}
		}
		
		foreach($status as $user_id => $status)
		{
			if($status == ilParticipationStatus::STATUS_NOT_SET)
			{
				$status = null;
			}
			
			$user_points = $points[$user_id];
			if($user_points === "")
			{
				$user_points = null;
			}
			
			// gev-patch start (#640)
			if (   $status == ilParticipationStatus::STATUS_ABSENT_EXCUSED
				|| $status == ilParticipationStatus::STATUS_ABSENT_NOT_EXCUSED) {
				$user_points = 0;
			}
			// gev-patch end
			
			$this->getParticipationStatus()->setStatus($user_id, $status);
			$this->getParticipationStatus()->setCreditPoints($user_id, $user_points);
		}	
		
		if(!$a_return)
		{
			ilUtil::sendSuccess($lng->txt("settings_saved"), true);
			// gev-patch start
			//$ilCtrl->redirect($this, "listStatus");
			$this->returnToList();
			// gev-patch end
		}		
	}	
	
	/**
	 * Confirm finalize
	 */
	protected function confirmFinalize()
	{
		global $tpl, $ilCtrl, $lng;
		
		$invalid = $this->saveStatusAndPoints(true);
		if(is_array($invalid))
		{
			return $this->listStatus($invalid);
		}
				
		if(!$this->getParticipationStatus()->allStatusSet())
		{
			ilUtil::sendFailure($lng->txt("ptst_admin_finalize_need_not_status_set"), true);
			// gev-patch start
			//$ilCtrl->redirect($this, "listStatus");
			$this->returnToList();
			// gev-patch end
		}
				
		$helper = ilParticipationStatusHelper::getInstance($this->getCourse());
		if($helper->getCourseNeedsAttendanceList() &&
			!$this->getParticipationStatus()->getAttendanceList())
		{
			ilUtil::sendFailure($lng->txt("ptst_admin_finalize_need_attendance_list"), true);
			// gev-patch start
			//$ilCtrl->redirect($this, "listStatus");
			$this->returnToList();
			// gev-patch end
		}
		
		
		// confirmation 
		// gev-patch start
		/*
		$this->setTabs("listStatus");
		
		include_once "./Services/Utilities/classes/class.ilConfirmationGUI.php";
		$confirm = new ilConfirmationGUI();
		$confirm->setFormAction($ilCtrl->getFormAction($this, "finalize"));
		$confirm->setHeaderText($lng->txt("ptst_admin_confirm_finalize"));
		$confirm->setConfirm($lng->txt("confirm"), "finalize");
		$confirm->setCancel($lng->txt("cancel"), "listStatus");				
		$tpl->setContent($confirm->getHTML());	
		*/
		include_once "./Services/Utilities/classes/class.ilConfirmationGUI.php";
		$confirm = new ilConfirmationGUI();
		$confirm->setHeaderText($lng->txt("ptst_admin_confirm_finalize"));
		$confirm->setConfirm($lng->txt("confirm"), "finalize");
		$confirm->setCancel($lng->txt("cancel"), "listStatus");

		require_once("Services/GEV/Utils/classes/class.gevCourseUtils.php");
		$crs_utils = gevCourseUtils::getInstanceByObj($this->getCourse());
		$min_parti = ($crs_utils->getMinParticipants() === null) ? 0 : $crs_utils->getMinParticipants();
		$succ_parti = $crs_utils->getSuccessfullParticipants();

		if($min_parti > 0 && $min_parti > count($succ_parti)) {
			$confirm->addItem("",
				"",
				sprintf($lng->txt("gev_training_min_participation_count_not_reached"),$min_parti)

			);
		}

		if(!$this->from_foreign_class){
			$this->setTabs("listStatus");
			$confirm->setFormAction($ilCtrl->getFormAction($this, "finalize"));
			$tpl->setContent($confirm->getHTML());	
		} else {
			$frm_action = $ilCtrl->getFormAction($this, "finalize");
			$frm_action .= '&crsrefid=' .$this->crs_ref_id;
			$confirm->setFormAction($frm_action);
			return $tpl->setContent($confirm->getHTML());	
		}
		// gev-patch end


		
		
	}
	
	/**
	 * Finalize status
	 */
	protected function finalize()
	{		
		global $ilCtrl, $lng;
		
		if($this->getParticipationStatus()->finalizeProcessState())
		{
			ilUtil::sendSuccess($lng->txt("settings_saved"), true);			
		}		
		// gev-patch start
		else 
		{
			ilUtil::sendFailure($lng->txt("settings_not_saved"), true);			
		}
		//$ilCtrl->redirect($this, "listStatus");
		$this->returnToList();
		// gev-patch end
	}
	
	
	//
	// ATTENDANCE LIST ACTIONS
	//
	
	/**
	 * Upload attendance list file
	 */
	protected function uploadAttendanceList()
	{
		global $ilCtrl, $lng;
		
		if(!$_FILES["atlst"]["tmp_name"])
		{
			$ilCtrl->redirect($this, "listStatus");
		}					
		
		if($this->getParticipationStatus()->uploadAttendanceList($_FILES["atlst"]))
		{
			ilUtil::sendSuccess($lng->txt("settings_saved"), true);			
		}		
		$ilCtrl->redirect($this, "listStatus");
	}
	
	/**
	 * View/download attendance list file
	 */
	protected function viewAttendanceList()
	{
		global $ilCtrl;
		
		$list = $this->getParticipationStatus()->getAttendanceList();
		if(!$list)
		{
			$ilCtrl->redirect($this, "listStatus");
		}	
		
		ilUtil::deliverFile($list, basename($list));	
	}
	
	/**
	 * Delete attendance list file
	 */
	protected function deleteAttendanceList()
	{
		global $ilCtrl;
		
		$list = $this->getParticipationStatus()->getAttendanceList();
		if($list)
		{
			$this->getParticipationStatus()->deleteAttendanceList();			
		}	
		
		$ilCtrl->redirect($this, "listStatus");
	}	

	// gev-patch start
	protected function saveSendMailDate() {
		global $lng;
		
		if (!array_key_exists("mail_send_confirm", $_POST) || !$this->mayWrite()) {
			ilUtil::sendFailure($lng->txt("gev_psstatus_mail_send_date_error"), true);
		}
		else {
			$d = $_POST["mail_send_at"]["date"];
			$date_set = $d["y"]."-".str_pad($d["m"], 2, '0', STR_PAD_LEFT)."-".str_pad($d["d"], 2, '0', STR_PAD_LEFT);
			
			$helper = ilParticipationStatusHelper::getInstance($this->getCourse());
			$date_tr = $helper->getCourseStart();
			$date_tr->increment(ilDateTime::DAY, -3);
			if ($date_tr->get(IL_CAL_DATE) < $date_set) {
				ilUtil::sendFailure($lng->txt("gev_psstatus_mail_send_date_invalid"), true);
			}
			else {
				$this->getParticipationStatus()->setMailSendDate($date_set);
				ilUtil::sendSuccess($lng->txt("gev_psstatus_mail_send_date_success"), true);
			}
		}
		$this->returnToList();
	}
	// gev-patch end
}

