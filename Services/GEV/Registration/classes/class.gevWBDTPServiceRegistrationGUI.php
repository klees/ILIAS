<?php

/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */#

/**
* GUI for registering WBD-relevant information of a user.
*
* @author	Richard Klees <richard.klees@concepts-and-training.de>
* @version	$Id$
*/

require_once("Services/GEV/Utils/classes/class.gevUserUtils.php");
require_once("Services/GEV/Utils/classes/class.gevSettings.php");
require_once("Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php");
require_once("Services/Form/classes/class.ilPropertyFormGUI.php");
require_once("Services/Form/classes/class.ilCheckboxInputGUI.php");
require_once("Services/Form/classes/class.ilRadioGroupInputGUI.php");
require_once("Services/Form/classes/class.ilEMailInputGUI.php");
require_once("Services/Form/classes/class.ilRadioOption.php");
require_once("Services/Form/classes/class.ilTextInputGUI.php");

class gevWBDTPServiceRegistrationGUI {
	public function __construct() {
		global $lng, $ilCtrl, $ilLog, $ilUser;

		$this->lng = &$lng;
		$this->ctrl = &$ilCtrl;
		$this->log = &$ilLog;
		$this->user = &$ilUser;
		$this->user_utils = gevUserUtils::getInstanceByObj($this->user);
	}

	public function executeCommand() {
		$this->checkWBDRelevantRole();
		$cmd = $this->ctrl->getCmd();

		switch ($cmd) {
			case "setBWVId":
			case "noBWVId":
			case "registerTPService":
			case "noServiceReg":
			case "createTPServiceBWVId":
			case "affiliateUser":
				$ret = $this->$cmd();
				break;
			default:
				$ret = $this->createTPServiceRegistrationStart();
		}

		require_once("Services/CaTUIComponents/classes/class.catTitleGUI.php");
		$title = new catTitleGUI("gev_wbd_registration"
								, "gev_wbd_registration_header_note"
								, "GEV_img/ico-head-wbd_registration.png"
								);

		return    $title->render()
				. $ret
				;
	}

	protected function checkWBDRelevantRole() {
		if (!$this->user_utils->hasWBDRelevantRole()) {
			$this->redirectToBookingOr("");
			exit();
		}
	}

	protected function checkAlreadyRegistered() {
		if ($this->user_utils->hasDoneWBDRegistration()) {
			$this->redirectToBookingOr("");
			exit();
		}
	}

	protected function redirectToBookingOr($a_target) {
		require_once("Services/Authentication/classes/class.ilSession.php");
		$after_registration = ilSession::get("gev_after_registration");
		if ($after_registration) {
			ilUtil::redirect($after_registration);
		}
		else {
			ilUtil::redirect($a_target);
		}
	}

	protected function setBWVId() {
		if (!gevUserUtils::isValidBWVId($_POST["bwv_id"])) {
			ilUtil::sendFailure($this->lng->txt("gev_bwv_id_input_not_valid"));
			
			return $this->noServiceReg();
		}

		if ($_POST["wbd_acceptance"] != 1) {
			ilUtil::sendFailure($this->lng->txt("gev_needs_wbd_acceptance"));
			return $this->noServiceReg();
		}

		$this->user_utils->setWBDBWVId($_POST["bwv_id"]);
		$this->user_utils->setWBDTPType(gevUserUtils::WBD_EDU_PROVIDER);
		$this->user_utils->setWBDRegistrationDone();
		
		$usr = new ilObjUser($this->user_utils->getUser()->getId());
		$usr->update();
		
		ilUtil::sendSuccess($this->lng->txt("gev_wbd_registration_finished_has_bwv_id_service"), true);
		$this->redirectToBookingOr("");
	}

	protected function noBWVId() {
		$this->user_utils->setRawWBDOKZ(gevUserUtils::WBD_NO_OKZ);
		$this->user_utils->setWBDRegistrationDone();
		ilUtil::sendSuccess($this->lng->txt("gev_wbd_registration_finished_no_bwv_id_service"), true);
		$this->redirectToBookingOr("");
	}

	protected function createTPServiceBWVId() {
		if($_POST["registration_type"] == "new") {
			return $this->newWBDAccount();
		}

		if($_POST["registration_type"] == "exist") {
			return $this->existingWBDAccount();
		}
		
		return $this->createTPServiceRegistrationStart();
	}

	protected function newWBDAccount($a_form = null) {
		$tpl = new ilTemplate("tpl.gev_wbd_tp_service_form.html", false, false, "Services/GEV/Registration");
		$form = $a_form===null ? $this->buildTPServiceForm() : $a_form;
		$tpl->setVariable("ACTION", $this->ctrl->getFormAction($this));
		$tpl->setVariable("FORM", $form->getHTML());

		return $tpl->get();
	}

	protected function existingWBDAccount($a_form = null) {
		$tpl = new ilTemplate("tpl.gev_wbd_tp_exist_form.html", false, false, "Services/GEV/Registration");
		$form = $a_form===null ? $this->buildCreateWBDAccountExistForm() : $a_form;
		$tpl->setVariable("ACTION", $this->ctrl->getFormAction($this));
		$tpl->setVariable("FORM", $form->getHTML());

		return $tpl->get();
	}

	protected function createTPServiceRegistrationStart($a_form = null) {
		$tpl = new ilTemplate("tpl.gev_wbd_tp_start_form.html", false, false, "Services/GEV/Registration");
		$form = $a_form===null ? $this->buildRegistrationStartForm() : $a_form;
		$tpl->setVariable("ACTION", $this->ctrl->getFormAction($this));
		$tpl->setVariable("FORM", $form->getHTML());
		$tpl->setVariable("NO_BWV_SERVICE_REG_LNK", $this->ctrl->getLinkTarget($this, 'noServiceReg'));

		return $tpl->get();
	}

	protected function registerTPService() {
		$form = $this->buildTPServiceForm();

		$err = false;
		$form->setValuesByPost();
		if (!$form->checkInput()) {
			$err = true;
		}
		else {
			for ($i = 1; $i <= 4; ++$i) {
				$chb = $form->getItemByPostVar("chb".$i);
				if (!$chb->getChecked()) {
					$err = true;
					$chb->setAlert($this->lng->txt("gev_wbd_registration_cb_mandatory"));
				}
			}
		}

		if ($err) {
			return $this->newWBDAccount($form);
		}

		$this->user_utils->setNextWBDAction(gevSettings::USR_WBD_NEXT_ACTION_NEW_TP_SERVICE);

		if ($form->getInput("notifications") == "diff") {
			$this->user_utils->setWBDCommunicationEmail($form->getInput("email"));
		}

		$this->user_utils->setWBDRegistrationDone();

		$usr = new ilObjUser($this->user_utils->getUser()->getId());
		$usr->update();

		ilUtil::sendSuccess($this->lng->txt("gev_wbd_registration_finished_create_bwv_id"), true);
				
		$this->redirectToBookingOr("ilias.php?baseClass=gevDesktopGUI&cmdClass=toMyCourses");
	}

	protected function affiliateUser() {
		$form = $this->buildCreateWBDAccountExistForm();

		$err = false;
		$form->setValuesByPost();

		if(!$form->checkInput()){
			$err = true;
		}
		
		for ($i = 1; $i <= 4; ++$i) {
			$chb = $form->getItemByPostVar("chb".$i);
			if (!$chb->getChecked()) {
				$err = true;
				$chb->setAlert($this->lng->txt("gev_wbd_affiliate_cb_mandatory"));
			}
		}

		if ($err) {
			return $this->existingWBDAccount($form);
		}

		$this->user_utils->setWBDTPType(gevUserUtils::WBD_EDU_PROVIDER);
		$this->user_utils->setNextWBDAction(gevSettings::USR_WBD_NEXT_ACTION_AFFILIATE);
		$this->user_utils->setWBDBWVId($form->getInput("bwv_id"));
		$this->user_utils->setTPServiceOld($form->getInput("tp_service_old"));

		if ($form->getInput("notifications") == "diff") {
			$this->user_utils->setWBDCommunicationEmail($form->getInput("email"));
		}

		$this->user_utils->setWBDRegistrationDone();

		$usr = new ilObjUser($this->user_utils->getUser()->getId());
		$usr->update();

		ilUtil::sendSuccess($this->lng->txt("gev_wbd_affiliate_finished"), true);
				
		$this->redirectToBookingOr("ilias.php?baseClass=gevDesktopGUI&cmdClass=toMyCourses");
	}

	protected function noServiceReg() {
		$tpl = new ilTemplate("tpl.gev_wbd_tp_service_form_noreg.html", false, false, "Services/GEV/Registration");
		
		$tpl->setVariable("ACTION", $this->ctrl->getFormAction($this));

		$chb = new ilCheckboxInputGUI("", "wbd_acceptance");
		$chb->setOptionTitle($this->lng->txt("evg_wbd"));
		$chb->setRequired(true);
		$tpl->setVariable("WBD_ACCEPTANCE_CHECKBOX", $chb->render());
		
		$tpl->setVariable("QUESTION", $this->lng->txt("gev_wbd_registration_question_service"));
		$tpl->setVariable("HAS_BWV_ID", $this->lng->txt("gev_wbd_registration_has_bwv_id"));
		$tpl->setVariable("HAS_BWV_ID_COMMAND", $this->lng->txt("gev_wbd_registration_has_bwv_id_cmd_service"));
		$tpl->setVariable("NO_BWV_ID", $this->lng->txt("gev_wbd_registration_no_bwv_id"));
		$tpl->setVariable("NO_BWV_ID_COMMAND", $this->lng->txt("gev_wbd_registration_no_bwv_id_cmd"));

		return $tpl->get();
	}

	protected function buildTPServiceForm() {
		$form = new ilPropertyFormGUI();
		$form->addCommandButton("registerTPService", $this->lng->txt("register_tp_service"));
		$form->addCommandButton("createTPServiceRegistrationStart", $this->lng->txt("back"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		$wbd_link = "<a href='/Customizing/global/skin/genv/static/documents/02_AGB_WBD.pdf' target='_blank' class='blue'>".$this->lng->txt("gev_agb_wbd")."</a>";
		$auftrag_link = $this->lng->txt("gev_mandate");
		$agb_link = "<a href='/Customizing/global/skin/genv/static/documents/01_AGB_TGIC.pdf' target='_blank' class='blue'>".$this->lng->txt("gev_agb_tgic")."</a>";

		$chb1 = new ilCheckboxInputGUI("", "chb1");
		$chb1->setOptionTitle(sprintf($this->lng->txt("gev_give_mandate_tp_service"), $auftrag_link));
		$form->addItem($chb1);

		$chb2 = new ilCheckboxInputGUI("", "chb2");
		$chb2->setOptionTitle(sprintf($this->lng->txt("gev_confirm_wbd"), $wbd_link));
		$form->addItem($chb2);

		$chb3 = new ilCheckboxInputGUI("", "chb3");
		$chb3->setOptionTitle(sprintf($this->lng->txt("gev_confirm_agb"), $agb_link));
		$form->addItem($chb3);

		$chb4 = new ilCheckboxInputGUI("", "chb4");
		$chb4->setOptionTitle($this->lng->txt("gev_no_other_wbd_mandate"));
		$form->addItem($chb4);

		$opt1 = new ilRadioGroupInputGUI($this->lng->txt("gev_wbd_notifications"), "notifications");
		$opt1->addOption(new ilRadioOption($this->lng->txt("gev_wbd_notifications_to_auth"), "auth"));
		$extra = new ilRadioOption($this->lng->txt("gev_wbd_notifications_to_diff"), "diff");
		$email = new ilEMailInputGUI($this->lng->txt("gev_alternative_email"), "email");
		$email->setRequired(true);
		$extra->addSubItem($email);
		$opt1->addOption($extra);
		$opt1->setValue("auth");
		$form->addItem($opt1);

		return $form;
	}

	protected function buildCreateWBDAccountExistForm() {
		$form = new ilPropertyFormGUI();
		$form->addCommandButton("affiliateUser", $this->lng->txt("gev_wbd_register_affiliate"));
		$form->addCommandButton("createTPServiceRegistrationStart", $this->lng->txt("back"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		$wbd_link = "<a href='/Customizing/global/skin/genv/static/documents/02_AGB_WBD.pdf' target='_blank' class='blue'>".$this->lng->txt("gev_agb_wbd")."</a>";
		$agb_link = "<a href='/Customizing/global/skin/genv/static/documents/01_AGB_TGIC.pdf' target='_blank' class='blue'>".$this->lng->txt("gev_agb_tgic")."</a>";
		$auftrag_link = $this->lng->txt("gev_mandate");

		$chb1 = new ilCheckboxInputGUI("", "chb1");
		$chb1->setOptionTitle(sprintf($this->lng->txt("gev_give_affiliate_tp_service"), $auftrag_link));
			$old_tp_service = new ilTextInputGUI($this->lng->txt("gev_give_affiliate_tp_service_old"),"tp_service_old");
			$old_tp_service->setRequired(true);
		$chb1->addSubItem($old_tp_service);
		$form->addItem($chb1);

		$chb2 = new ilCheckboxInputGUI("", "chb2");
		$chb2->setOptionTitle($this->lng->txt("gev_give_affiliate_wbd"));
		$form->addItem($chb2);

		$chb3 = new ilCheckboxInputGUI("", "chb3");
		$chb3->setOptionTitle(sprintf($this->lng->txt("gev_confirm_wbd"), $wbd_link));
		$form->addItem($chb3);

		$chb4 = new ilCheckboxInputGUI("", "chb4");
		$chb4->setOptionTitle(sprintf($this->lng->txt("gev_confirm_agb"), $agb_link));
		$form->addItem($chb4);

		$bwv_id = new ilTextInputGUI($this->lng->txt("gev_wbd_register_my_bwv_id"),"bwv_id");
		$bwv_id->setRequired(true);
		$form->addItem($bwv_id);

		//Options for option group
		$diff = new ilRadioOption($this->lng->txt("gev_wbd_notifications_to_diff"), "diff");
			$email = new ilEMailInputGUI($this->lng->txt("gev_alternative_email"), "email");
			$email->setRequired(true);
		$diff->addSubItem($email);

		$auth = new ilRadioOption($this->lng->txt("gev_wbd_notifications_to_auth"), "auth");
		
		//Optiongroup
		$opt1 = new ilRadioGroupInputGUI($this->lng->txt("gev_wbd_notifications"), "notifications");
		$opt1->addOption($auth);
		$opt1->addOption($diff);
		$opt1->setValue("auth");
		$form->addItem($opt1);

		return $form;
	}

	protected function buildRegistrationStartForm() {
		$form = new ilPropertyFormGUI();
		$form->addCommandButton("createTPServiceBWVId", $this->lng->txt("btn_next"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		$opt1 = new ilRadioGroupInputGUI($this->lng->txt("gev_wbd_register_selections"), "registration_type");
		$opt1->addOption(new ilRadioOption($this->lng->txt("gev_wbd_register_new"), "new"));
		$opt1->addOption(new ilRadioOption($this->lng->txt("gev_wbd_register_exist"), "exist"));
		$opt1->setValue("new");
		$form->addItem($opt1);

		return $form;
	}

}
?>