<?php

/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */#

/**
* Utilities for generali users.
*
* @author	Richard Klees <richard.klees@concepts-and-training.de>
* @version	$Id$
*/

require_once("Services/Calendar/classes/class.ilDateTime.php");
require_once("Services/Calendar/classes/class.ilDate.php");
require_once("Services/CourseBooking/classes/class.ilCourseBooking.php");
require_once("Services/CourseBooking/classes/class.ilUserCourseBookings.php");
require_once("Services/GEV/Utils/classes/class.gevAMDUtils.php");
require_once("Services/GEV/Utils/classes/class.gevUDFUtils.php");
require_once("Services/GEV/Utils/classes/class.gevDBVUtils.php");
require_once("Services/GEV/Utils/classes/class.gevCourseUtils.php");
require_once("Services/GEV/Utils/classes/class.gevSettings.php");
require_once("Services/GEV/Utils/classes/class.gevRoleUtils.php");
require_once("Services/GEV/Utils/classes/class.gevGeneralUtils.php");


class gevUserUtils {
	static protected $instances = array();


	// wbd stuff
	const WBD_NO_SERVICE 		= "0 - kein Service";
	const WBD_EDU_PROVIDER		= "1 - Bildungsdienstleister";
	const WBD_TP_BASIS			= "2 - TP-Basis";
	const WBD_TP_SERVICE		= "3 - TP-Service";
	
	const WBD_OKZ_FROM_POSITION	= "0 - aus Rolle";
	const WBD_OKZ1				= "1 - OKZ1";
	const WBD_OKZ2				= "2 - OKZ2";
	const WBD_OKZ3				= "3 - OKZ3";
	const WBD_NO_OKZ			= "4 - keine Zuordnung";
	
	const WBD_AGENTSTATUS0	= "0 - aus Rolle";
	const WBD_AGENTSTATUS1	= "1 - Angestellter Außendienst";
	const WBD_AGENTSTATUS2	= "2 - Ausschließlichkeitsvermittler";
	const WBD_AGENTSTATUS3	= "3 - Makler";
	const WBD_AGENTSTATUS4	= "4 - Mehrfachagent";
	const WBD_AGENTSTATUS5	= "5 - Mitarbeiter eines Vermittlers";
	const WBD_AGENTSTATUS6	= "6 - Sonstiges";
	const WBD_AGENTSTATUS7	= "7 - keine Zuordnung";
	
	const WBD_EMPTY_BWV_ID = "-empty-";

	const WBD_ERROR_WRONG_USERDATA 		= 'WRONG_USERDATA'; 
	const WBD_ERROR_USER_SERVICETYPE 	= 'USER_SERVICETYPE'; 
	const WBD_ERROR_USER_DIFFERENT_TP 	= 'USER_DIFFERENT_TP'; 
	const WBD_ERROR_USER_UNKNOWN 		= 'USER_UNKNOWN';
	const WBD_ERROR_USER_DEACTIVATED 	= 'USER_DEACTIVATED';
	const WBD_ERROR_NO_RELEASE			= 'NO_RELEASE';

	static $wbd_agent_status_mapping = array(
		//1 - Angestellter Außendienst
		self::WBD_AGENTSTATUS1 => array(
			/* GOA V1:
			"OD/LD/BD/VD/VTWL"
			,"DBV/VL-EVG"
			,"DBV-UVG"
			*/
			"OD /BD"
			,"OD/BD"
			,"FD"
			,"Org PV 59"
			,"PV 59"
			,"Ausbildungsbeauftragter"
			,"VA 59"
			,"VA HGB 84"
			,"NFK"
			,"OD-Betreuer"
			,"DBV UVG"
			,"DBV EVG"
		),

		//2 - Ausschließlichkeitsvermittler
		self::WBD_AGENTSTATUS2 => array(
			/* GOA V1:
			"AVL"
			,"HA"
			,"BA"
			,"NA"
			*/
			"UA"
			,"HA 84"
			,"BA 84"
			,"NA"
			,"AVL" 
		),
		//3 - Makler
		self::WBD_AGENTSTATUS3 => array(
			"VP"
		),

		//5 - Mitarbeiter eines Vermittlers
		self::WBD_AGENTSTATUS5 => array(
			"Agt-ID"
		),
		//6 - Sonstiges
		self::WBD_AGENTSTATUS6 => array(
			'Administrator'
			,'Admin-Voll'
			,'Admin-eingeschraenkt'
			,'Admin-Ansicht'
			,'ID FK'
			,'ID MA'
			,'OD/FD/BD ID'
			,'FDA'
			,'Ausbilder'
			,'Azubi'
			,'Veranstalter'
			,'int. Trainer'
			,'ext. Trainer'
			,'TP Service'
			,'TP Basis'
			,'VFS'

		)

	);

	/* GOA V1:
	static $wbd_relevant_roles	= array( //"OD/LD/BD/VD/VTWL"
									     "DBV/VL-EVG"
									   , "DBV-UVG"
									   , "AVL"
									   , "HA"
									   , "BA"
									   //, "NA"
									   , "VP"
									   , "TP-Basis Registrierung"
									   , "TP-Service Registrierung"
									   );

	static $wbd_tp_service_roles = array( //"OD/LD/BD/VD/VTWL"
									     "DBV/VL-EVG"
									   , "DBV-UVG"
									   , "AVL"
									   , "HA"
									   , "BA"
									   //, "NA"
									   , "TP-Service Registrierung"
									   );
	*/

	static $wbd_tp_service_roles = array(
		"UA"
		,"HA 84"
		,"BA 84"
		,"Org PV 59"
		,"PV 59"
		,"AVL"
		,"DBV UVG"
		,"DBV EVG"
		,"TP Service"
	);
	
	static $wbd_relevant_roles = array(
		"UA"
		,"HA 84"
		,"BA 84"
		,"Org PV 59"
		,"PV 59"
		,"AVL"
		,"DBV UVG"
		,"DBV EVG"
		,"TP Service"
		,"TP Basis"
		,"VP"
	);

	
	// Für diese Rollen wird bei der Selbstbuchung der Hinweis "Vorabendanreise 
	// mit Führungskraft klären" angezeigt.
	static $roles_with_prearrival_note = array(
		  "UA"
		, "HA 84"
		, "BA 84"
		, "Org PV 59"
		, "PV 59"
		, "ID MA"
		, "OD/FD/BD ID"
		, "Agt-ID"
		, "VA 59"
		, "VA HGB 84"
		, "NFK"
		, "FDA"
		, "Azubi"
		, "DBV UVG"
		, "DBV EVG"
	);


	


	protected function __construct($a_user_id) {
		global $ilDB;
		global $ilAccess;
		
		$this->user_id = $a_user_id;
		$this->courseBookings = ilUserCourseBookings::getInstance($a_user_id);
		$this->gev_set = gevSettings::getInstance();
		$this->udf_utils = gevUDFUtils::getInstance();
		$this->db = &$ilDB;
		$this->access = &$ilAccess;
		$this->user_obj = null;
		$this->org_id = null;
		$this->direct_superior_ous = null;
		$this->direct_superior_ou_names = null;
		$this->superior_ous = null;
		$this->superior_ou_names = null;
		$this->edu_bio_ou_names = null;
		$this->edu_bio_ou_ref_ids_empl = null;
		$this->edu_bio_ou_ref_ids_all = null;
		$this->edu_bio_ou_ref_ids = null;
		$this->edu_bio_usr_ids = null;
		$this->employees_active = null;
		$this->employees_all = null;
		$this->employees_for_course_search = null;
		$this->employee_ids_for_course_search = null;
		$this->employees_for_booking_cancellations = null;
		$this->employee_ids_for_booking_cancellations = null;
		$this->employee_ids_for_booking_view = null;
		$this->employee_ous = null;
		$this->employees_ou_names = null;
		$this->od = false;
		
		$this->potentiallyBookableCourses = array();
		$this->users_who_booked_at_course = array();
	}
	
	public function getUser() {
		require_once("Services/User/classes/class.ilObjUser.php");
		
		if ($this->user_obj === null) {
			$this->user_obj = new ilObjUser($this->user_id);
		}
		
		return $this->user_obj;
	}
	
	public function getId() {
		return $this->user_id;
	}
	
	static public function getInstance($a_user_id) {
		if($a_user_id === null) {
			throw new Exception("gevUserUtils::getInstance: ".
								"No User-ID given.");
		}

		if(!self::userIdExists($a_user_id)) {
			throw new Exception("gevUserUtils::getInstance: ".
									"User with ID '".$a_user_id."' does not exist.");
		}

		if (array_key_exists($a_user_id, self::$instances)) {
			return self::$instances[$a_user_id];
		}

		self::$instances[$a_user_id] = new gevUserUtils($a_user_id);
		return self::$instances[$a_user_id];
	}
	
	static public function getInstanceByObj(ilObjUser $a_user_obj) {
		$inst = self::getInstance($a_user_obj->getId());
		$inst->user_obj = $a_user_obj;
		return $inst;
	}
	
	static public function getInstanceByObjOrId($a_user) {
		if (is_int($a_user) || is_numeric($a_user)) {
			return self::getInstance((int)$a_user);
		}
		else {
			return self::getInstanceByObj($a_user);
		}
	}

	static public function userIdExists($a_user_id) {
		global $ilDB;

		$sql = "SELECT usr_id FROM usr_data WHERE usr_id = ".$ilDB->quote($a_user_id, "integer");
		$res = $ilDB->query($sql);

		if($ilDB->numRows($res) == 0) {
			return false;
		}

		return true;
	}
	
	public function getNextCourseId() {
		$now = date("Y-m-d");
		$crss = $this->getBookedAndWaitingCourses();
		$amd = array( gevSettings::CRS_AMD_START_DATE => "start_date");
		$info = gevAMDUtils::getInstance()->getTable($crss, $amd, array(), array(),
													 " AND amd0.value >= ".$this->db->quote($now, "text").
													 " ORDER BY start_date ASC".
													 " LIMIT 1 OFFSET 0");
		if (count($info) > 0) {
			$val = array_pop($info);
			return $val["obj_id"];
		}
		else {
			return null;
		}
	}
	
	public function getLastCourseId() {
		$now = date("Y-m-d");
		$crss = $this->getBookedCourses();
		$amd = array( gevSettings::CRS_AMD_START_DATE => "start_date");
		$info = gevAMDUtils::getInstance()->getTable($crss, $amd, array(), array(),
													 " AND amd0.value < ".$this->db->quote($now, "text").
													 " ORDER BY start_date DESC".
													 " LIMIT 1 OFFSET 0");
		if (count($info) > 0) {
			$val = array_pop($info);
			return $val["obj_id"];
		}
		else {
			return null;
		}
	}
	
	public function getEduBioLink() {
		return self::getEduBioLinkFor($this->user_id);
	}
	
	static public function getEduBioLinkFor($a_target_user_id) {
		global $ilCtrl;
		$ilCtrl->setParameterByClass("gevEduBiographyGUI", "target_user_id", $a_target_user_id);
		$link = $ilCtrl->getLinkTargetByClass("gevEduBiographyGUI", "view");
		$ilCtrl->clearParametersByClass("gevEduBiographyGUI");
		return $link;
	}

	public function filter_for_online_courses($ar){
		/*
		check, if course exists and is online;
		*/
		require_once("Services/GEV/Utils/classes/class.gevObjectUtils.php");
		
		$ret = array();
		foreach ($ar as $crsid) {
			if(gevObjectUtils::checkObjExistence($crsid)){
				$crs_utils = gevCourseUtils::getInstance($crsid);
				if ($crs_utils->getCourse()->isActivated()){
					$ret[] = $crsid;
				} 
			}
		}
		return $ret;
	}
	
	public function getBookedAndWaitingCourseInformation() {
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		
		$crs_amd = 
			array( gevSettings::CRS_AMD_START_DATE			=> "start_date"
				 , gevSettings::CRS_AMD_END_DATE 			=> "end_date"
				 , gevSettings::CRS_AMD_CANCEL_DEADLINE		=> "cancel_date"
				 , gevSettings::CRS_AMD_ABSOLUTE_CANCEL_DEADLINE => "absolute_cancel_date"
				 , gevSettings::CRS_AMD_SCHEDULED_FOR		=> "scheduled_for"
				 , gevSettings::CRS_AMD_SCHEDULE			=> "crs_amd_schedule"
				 //, gevSettings::CRS_AMD_ => "title"
				 //, gevSettings::CRS_AMD_START_DATE => "status"
				 , gevSettings::CRS_AMD_TYPE 				=> "type"
				 , gevSettings::CRS_AMD_VENUE 				=> "location"
				 , gevSettings::CRS_AMD_VENUE_FREE_TEXT 	=> "location_free_text"
				 , gevSettings::CRS_AMD_CREDIT_POINTS 		=> "points"
				 , gevSettings::CRS_AMD_FEE					=> "fee"
				 , gevSettings::CRS_AMD_TARGET_GROUP_DESC	=> "target_group"
				 , gevSettings::CRS_AMD_TARGET_GROUP		=> "target_group_list"
				 , gevSettings::CRS_AMD_GOALS 				=> "goals"
				 , gevSettings::CRS_AMD_CONTENTS 			=> "content"
			);
		
		
		$booked = $this->getBookedCourses();
		$booked = $this->filter_for_online_courses($booked);

		$booked_amd = gevAMDUtils::getInstance()->getTable($booked, $crs_amd);
		foreach ($booked_amd as $key => $value) {
			$booked_amd[$key]["status"] = ilCourseBooking::STATUS_BOOKED;
			$booked_amd[$key]["cancel_date"] = gevCourseUtils::mkDeadlineDate( $value["start_date"]
																			 , $value["cancel_date"]
																			 );
			$booked_amd[$key]["absolute_cancel_date"] = gevCourseUtils::mkDeadlineDate( $value["start_date"]
																					  , $value["absolute_cancel_date"]
																					  );
			// TODO: Push this to SQL-Statement.
			$orgu_utils = gevOrgUnitUtils::getInstance($value["location"]);
			$crs_utils = gevCourseUtils::getInstance($value["obj_id"]);
			$booked_amd[$key]["overnights"] = $this->getFormattedOvernightDetailsForCourse($crs_utils->getCourse());
			$booked_amd[$key]["location"] = $orgu_utils->getLongTitle();
			$booked_amd[$key]["fee"] = floatval($booked_amd[$key]["fee"]);
			$list = "";

			if(is_array($booked_amd[$key]["target_group_list"])) {
				foreach ($booked_amd[$key]["target_group_list"] as $val) {
					$list .= "<li>".$val."</li>";
				}
			}
			
			if($lis != "") {
				$booked_amd[$key]["target_group"] = "<ul>".$list."</ul>".$booked_amd[$key]["target_group"];
			}
		}


		$waiting = $this->getWaitingCourses();
		$waiting = $this->filter_for_online_courses($waiting);

		$waiting_amd = gevAMDUtils::getInstance()->getTable($waiting, $crs_amd);
		foreach ($waiting_amd as $key => $value) {
			$waiting_amd[$key]["status"] = ilCourseBooking::STATUS_WAITING;
			$waiting_amd[$key]["cancel_date"] = gevCourseUtils::mkDeadlineDate( $value["start_date"]
																			  , $value["cancel_date"]
																			  );
			$waiting_amd[$key]["absolute_cancel_date"] = gevCourseUtils::mkDeadlineDate( $value["start_date"]
																					   , $value["absolute_cancel_date"]
																					   );
			
			$orgu_utils = gevOrgUnitUtils::getInstance($value["location"]);
			$crs_utils = gevCourseUtils::getInstance($value["obj_id"]);
			$waiting_amd[$key]["overnights"] = $this->getFormattedOvernightDetailsForCourse($crs_utils->getCourse());
			$waiting_amd[$key]["location"] = $orgu_utils->getLongTitle();
			$waiting_amd[$key]["fee"] = floatval($waiting_amd[$key]["fee"]);
			$list = "";
			foreach ($waiting_amd[$key]["target_group_list"] as $val) {
				$list .= "<li>".$val."</li>";
			}
			$waiting_amd[$key]["target_group"] = "<ul>".$list."</ul>".$waiting_amd[$key]["target_group"];
		}
		
		return array_merge($booked_amd, $waiting_amd);
	}
	
	public function getCourseIdsWhereUserIsTutor() {
		require_once("Services/GEV/Utils/classes/class.gevSettings.php");
		
		$like_role = array();
		foreach (gevSettings::$TUTOR_ROLES as $role) {
			$like_role[] = "od.title LIKE ".$this->db->quote($role);
		}
		$like_role = implode(" OR ", $like_role);
		
		$tmplt_field_id = gevSettings::getInstance()->getAMDFieldId(gevSettings::CRS_AMD_IS_TEMPLATE);
		
		$res = $this->db->query(
			 "SELECT oref.obj_id, oref.ref_id "
			."  FROM object_reference oref"
			."  JOIN object_data od ON od.type = 'role' AND ( ".$like_role ." )"
			."  JOIN rbac_fa fa ON fa.rol_id = od.obj_id"
			."  JOIN tree tr ON tr.child = fa.parent"
			."  JOIN rbac_ua ua ON ua.rol_id = od.obj_id"
			."  JOIN object_data od2 ON od2.obj_id = oref.obj_id"
			." LEFT JOIN adv_md_values_text is_template "
			."    ON oref.obj_id = is_template.obj_id "
			."   AND is_template.field_id = ".$this->db->quote($tmplt_field_id, "integer")
			." WHERE oref.ref_id = tr.parent"
			."   AND ua.usr_id = ".$this->db->quote($this->user_id, "integer")
			."   AND od2.type = 'crs'"
			."   AND oref.deleted IS NULL"
			."   AND is_template.value = 'Nein'"
			);

		$crs_ids = array();
		while($rec = $this->db->fetchAssoc($res)) {
			//we need only one ref-id here
			$crs_ids[$rec['obj_id']] = $rec['ref_id'];
		}

		return $crs_ids;
	}

	public function getMyAppointmentsCourseInformation($a_order_field = null, $a_order_direction = null) {
			// used by gevMyTrainingsApTable, i.e.
		
			if ((!$a_order_field && $a_order_direction) || ($a_order_field && !$a_order_direction)) {
				throw new Exception("gevUserUtils::getMyAppointmentsCourseInformation: ".
									"You need to set bost: order_field and order_direction.");
			}
			
			if ($a_order_direction) {
				$a_order_direction = strtoupper($a_order_direction);
				if (!in_array($a_order_direction, array("ASC", "DESC"))) {
					throw new Exception("gevUserUtils::getMyAppointmentsCourseInformation: ".
										"order_direction must be ASC or DESC.");
				}
			}
			
			//require_once("Services/CourseBooking/classes/class.ilCourseBooking.php");
			require_once("Services/TEP/classes/class.ilTEPCourseEntries.php");
			require_once "Modules/Course/classes/class.ilObjCourse.php";
			require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
			require_once("Services/ParticipationStatus/classes/class.ilParticipationStatus.php");
			require_once("Services/ParticipationStatus/classes/class.ilParticipationStatusHelper.php");
			require_once("Services/ParticipationStatus/classes/class.ilParticipationStatusPermissions.php");
			
			$crss = $this->getCourseIdsWhereUserIsTutor();
			$crss_ids = array_keys($crss);
			
			//do the amd-dance
			$crs_amd = 
			array( gevSettings::CRS_AMD_START_DATE			=> "start_date"
				 , gevSettings::CRS_AMD_END_DATE 			=> "end_date"
				 
				 , gevSettings::CRS_AMD_CUSTOM_ID			=> "custom_id"
				 , gevSettings::CRS_AMD_TYPE 				=> "type"
				 
				 , gevSettings::CRS_AMD_VENUE 				=> "location"
				 , gevSettings::CRS_AMD_VENUE_FREE_TEXT 	=> "location_free_text"

				 , gevSettings::CRS_AMD_MAX_PARTICIPANTS	=> "mbr_max"
				 , gevSettings::CRS_AMD_MIN_PARTICIPANTS	=> "mbr_min"
				 
				 , gevSettings::CRS_AMD_TARGET_GROUP		=> "target_group"
				 , gevSettings::CRS_AMD_TARGET_GROUP_DESC	=> "target_group_desc"
				 , gevSettings::CRS_AMD_GOALS 				=> "goals"
				 , gevSettings::CRS_AMD_CONTENTS 			=> "content"
			);
			
			if ($a_order_field) {
				$order_sql = " ORDER BY ".$this->db->quoteIdentifier($a_order_field)." ".$a_order_direction;
			}
			else {
				$order_sql = "";
			}
			
			$crss_amd = gevAMDUtils::getInstance()->getTable($crss_ids, $crs_amd, array("pstatus.state pstate"),
				// Join over participation status table to remove courses, where state is already
				// finalized
				array(" LEFT JOIN crs_pstatus_crs pstatus ON pstatus.crs_id = od.obj_id "),
				" AND ( pstatus.state != ".$this->db->quote(ilParticipationStatus::STATE_FINALIZED, "integer").
			    "       OR pstatus.state IS NULL) ".$order_sql
				);

			$ret = array();

			foreach ($crss_amd as $id => $entry) {
				$entry['crs_ref_id'] = $crss[$id];

				$crs_utils = gevCourseUtils::getInstance($id);
				$orgu_utils = gevOrgUnitUtils::getInstance($entry["location"]);
				$ps_helper = ilParticipationStatusHelper::getInstance($crs_utils->getCourse());
				$ps_permission = ilParticipationStatusPermissions::getInstance($crs_utils->getCourse(), $this->user_id);

				$entry["location"] = $orgu_utils->getLongTitle();

				if($entry['start_date'] && $entry['end_date']) {
					$crs_obj = new ilObjCourse($crss[$id]);
					$tep_crsentries = ilTEPCourseEntries::getInstance($crs_obj);
					$tep_opdays_inst = $tep_crsentries->getOperationsDaysInstance();
					$tep_opdays = $tep_opdays_inst->getDaysForUser($this->user_id);
				} else {
					$tep_opdays =array();
				}
				
				$ms = $crs_utils->getMembership();
				$entry['mbr_booked_userids'] = $ms->getMembers();
				$entry['mbr_booked'] = count($entry['mbr_booked_userids']);
				$entry['mbr_waiting_userids'] = $crs_utils->getWaitingMembers($id);
				$entry['mbr_waiting'] = count($entry['mbr_waiting_userids']);
				$entry['apdays'] = $tep_opdays;
				//$entry['category'] = '-';
				
				$entry['may_finalize'] = $crs_utils->canModifyParticipationStatus($this->user_id);

				$ret[$id] = $entry;
			}

			//sort?
			return $ret;
	}
	
	public function getEmployeeIdsForBookingView() {
		if ($this->employee_ids_for_booking_view) {
			return $this->employee_ids_for_booking_view;
		}
		
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");

		// we need the employees in those ous
		$_d_ous = $this->getOrgUnitsWhereUserCanViewEmployeeBookings();
		// we need the employees in those ous and everyone in the ous
		// below those.
		$_r_ous = $this->getOrgUnitsWhereUserCanViewEmployeeBookingsRecursive();
		
		$e_ous = array_merge($_d_ous, $_r_ous);
		$a_ous = array();
		foreach(gevOrgUnitUtils::getAllChildren($_r_ous) as $val) {
			$a_ous[] = $val["ref_id"];
		}
		
		$e_ids = array_unique(array_merge( gevOrgUnitUtils::getEmployeesIn($e_ous)
										 , gevOrgUnitUtils::getAllPeopleIn($a_ous)
										 )
							 );
		$this->employee_ids_for_booking_view = $e_ids;
		
		return $e_ids;
	}
	
	public function getEmployeeIdsForBookingCancellations() {
		if ($this->employee_ids_for_booking_cancellations) {
			return $this->employee_ids_for_booking_cancellations;
		}
		
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");

		// we need the employees in those ous
		$_d_ous = $this->getOrgUnitsWhereUserCanCancelEmployeeBookings();
		// we need the employees in those ous and everyone in the ous
		// below those.
		$_r_ous = $this->getOrgUnitsWhereUserCanCancelEmployeeBookingsRecursive();
		
		$e_ous = array_merge($_d_ous, $_r_ous);
		$a_ous = array();
		foreach(gevOrgUnitUtils::getAllChildren($_r_ous) as $val) {
			$a_ous[] = $val["ref_id"];
		}
		
		$e_ids = array_unique(array_merge( gevOrgUnitUtils::getEmployeesIn($e_ous)
										 , gevOrgUnitUtils::getAllPeopleIn($a_ous)
										 )
							 );
		$this->employee_ids_for_booking_cancellations = $e_ids;
		
		return $e_ids;
	}
	
	public function getEmployeesForBookingCancellations() {
		if ($this->employees_for_booking_cancellations) {
			return $this->employees_for_booking_cancellations;
		}

		$e_ids = $this->getEmployeeIdsForBookingCancellations();
		
		$res = $this->db->query( "SELECT usr_id, firstname, lastname"
								." FROM usr_data "
								." WHERE ".$this->db->in("usr_id", $e_ids, false, "integer")
								);
		
		$this->employees_for_booking_cancellations = array();
		while($rec = $this->db->fetchAssoc($res)) {
			$this->employees_for_booking_cancellations[] = $rec;
		}
		
		return $this->employees_for_booking_cancellations;
	}

	public function forceWBDUserProfileFields() {
		return $this->hasWBDRelevantRole()
			&& $this->hasDoneWBDRegistration()
			&& (   $this->getWBDTPType() == self::WBD_TP_SERVICE
				|| $this->getWBDTPType() == self::WBD_TP_BASIS
				);
	}

	public function isProfileComplete() {
		if (!$this->forceWBDUserProfileFields()) {
			return true;
		}
		require_once("Services/GEV/Desktop/classes/class.gevUserProfileGUI.php");
		$email = $this->getEmail();
		$mobile = $this->getMobilePhone();
		$bday = $this->getUser()->getBirthday();
		$street = $this->getUser()->getStreet();
		$city = $this->getUser()->getCity();
		$zipcode = $this->getUser()->getZipcode();
		
		return $email && $mobile && preg_match(gevUserProfileGUI::$telno_regexp, $mobile)
				&& $mobile && $bday && $city && $zipcode;
	}
	
	
	public function getLogin() {
		return $this->getUser()->getLogin();
	}
	
	public function getGender() {
		return $this->getUser()->getGender();
	}
	
	public function getFirstname() {
		return $this->getUser()->getFirstname();
	}
	
	public function getLastname() {
		return $this->getUser()->getLastname();
	}
	
	public function getFullName() {
		return $this->getLastname().", ".$this->getFirstname();
	}
	
	static public function getFullNames($a_user_ids) {
		global $ilDB;
		
		$query = "SELECT usr_id, CONCAT(lastname, ', ', firstname) as fullname"
				."  FROM usr_data"
				." WHERE ".$ilDB->in("usr_id", $a_user_ids, false, "integer");
		$res = $ilDB->query($query);
		
		$ret = array();
		while($rec = $ilDB->fetchAssoc($res)) {
			$ret[$rec["usr_id"]] = $rec["fullname"];
		}
		return $ret;
	}
	
	public function getEMail() {
		return $this->getUser()->getEmail();
	}
	
	public function setEMail($email) {
		return $this->getUser()->setEmail($email);
	}
	
	public function getOrgUnitId() {
		if ($this->orgu_id === null) {
			$query = "SELECT oref.obj_id FROM object_data od "
					." JOIN rbac_ua ua ON od.obj_id = ua.rol_id "
					." JOIN object_reference oref ON oref.ref_id = SUBSTR(od.title, 18) "
					." WHERE od.type = 'role' " 
					." AND ua.usr_id = ".$this->db->quote($this->user_id, "integer")
					." AND od.title LIKE 'il_orgu_employee_%' "
					." AND oref.deleted IS NULL"
					." ORDER BY obj_id ASC LIMIT 1 OFFSET 0";
			
			$res = $this->db->query($query);
			if ($rec = $this->db->fetchAssoc($res)) {
				$this->orgu_id = $rec["obj_id"];
			}
			else {
				// Ok, so he is no employee. Maybe he's a superior?
				$query = "SELECT oref.obj_id FROM object_data od "
						." JOIN rbac_ua ua ON od.obj_id = ua.rol_id "
						." JOIN object_reference oref ON oref.ref_id = SUBSTR(od.title, 18) "
						." WHERE od.type = 'role' " 
						." AND ua.usr_id = ".$this->db->quote($this->user_id, "integer")
						." AND od.title LIKE 'il_orgu_superior_%' "
						." AND oref.deleted IS NULL"
						." ORDER BY obj_id ASC LIMIT 1 OFFSET 0";
				$res = $this->db->query($query);
				if ($rec = $this->db->fetchAssoc($res)) {
					return $rec["obj_id"];
				}
				else {
					// Oh no, he's not assigned anywhere....
					$this->orgu_id = null;
				}
			}
		}
		return $this->orgu_id;
	}
	
	public function getOrgUnitTitle() {
		$orgu_id = $this->getOrgUnitId();
		if ($orgu_id === null) {
			return "";
		}
		else {
			require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
			return gevOrgUnitUtils::getInstance($orgu_id)->getTitle();
		}
	}
	
	public function getODTitle() {
		$od = $this->getOD();
		if ($od === null) {
			return "";
		}
		return $od["title"];
	}
	
	public function getBirthday() {
		require_once("Services/Calendar/classes/class.ilDate.php");
		$bd = $this->getUser()->getBirthday();
		if (!is_a($bd, "ilDate")) {
			$bd = new ilDate($bd, IL_CAL_DATE);
		}
		return $bd;
	}
	
	public function getFormattedBirthday() {
		require_once("Services/Calendar/classes/class.ilDatePresentation.php");
		$date = ilDatePresentation::formatDate($this->getBirthday());
		return $date;
	}
	
	public function getADPNumberGEV() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_ADP_GEV_NUMBER);
	}

	public function setADPNumberGEV($a_adp) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_ADP_GEV_NUMBER, $a_adp);
	}	

	public function getADPNumberVFS() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_ADP_VFS_NUMBER);
	}
	
	public function setADPNumberVFS($a_adp) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_ADP_VFS_NUMBER, $a_adp);
	}
	
	public function getJobNumber() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_JOB_NUMMER);
	}
	
	public function setJobNumber($a_number) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_JOB_NUMMER, $a_number);
	}
	
	public function getBirthplace() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_BIRTHPLACE);
	}
	
	public function setBirthplace($a_place) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_BIRTHPLACE, $a_place);
	}
	
	public function getBirthname() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_BIRTHNAME);
	}
	
	public function setBirthname($a_name) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_BIRTHNAME, $a_name);
	}
	
	public function getIHKNumber() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_IHK_NUMBER);
	}
	
	public function setIHKNumber($a_number) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_IHK_NUMBER, $a_number);
	}
	
	public function getADTitle() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_AD_TITLE);
	}
	
	public function setADTitle($a_title) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_AD_TITLE, $a_title);
	}
	
	public function getAgentKey() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_AGENT_KEY);
	}
	
	public function setAgentKey($a_key) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_AGENT_KEY, $a_key);
	}

	public function getAgentKeyVFS() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_AGENT_KEY_VFS);
	}
	
	public function setAgentKeyVFS($a_key) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_AGENT_KEY_VFS, $a_key);
	}

	public function getAgentPositionVFS() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_AGENT_POSITION_VFS);
	}
	
	public function setAgentPositionVFS($a_key) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_AGENT_POSITION_VFS, $a_key);
	}

	
	/*
	public function getCompanyTitle() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_COMPANY_TITLE);
	}
	
	public function setCompanyTitle($a_title) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_COMPANY_TITLE, $a_title);
	}
	*/
	
	public function getCompanyName() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_COMPANY_NAME, $a_name);
	}

	public function setCompanyName($a_name) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_COMPANY_NAME, $a_name);
	}
	
	public function getPrivateStreet() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_PRIV_STREET);
	}
	
	public function setPrivateStreet($a_street) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_PRIV_STREET, $a_street);
	}
	
	public function getPrivateCity() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_PRIV_CITY);
	}
	
	public function setPrivateCity($a_city) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_PRIV_CITY, $a_city);
	}
	
	public function getPrivateZipcode() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_PRIV_ZIPCODE);
	}
	
	public function setPrivateZipcode($a_zipcode) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_PRIV_ZIPCODE, $a_zipcode);
	}
	

	public function getMobilePhone() {
		return $this->getUser()->getPhoneMobile();
	}
	public function setMobilePhone($a_phone) {
		return $this->getUser()->setPhoneMobile(a_phone);
	}

	/*
	public function getPrivatePhone() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_PRIV_PHONE);
	}
	public function setPrivatePhone($a_phone) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_PRIV_PHONE, $a_phone);
	}
	
	public function getPrivateState() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_PRIV_STATE);
	}
	
	public function setPrivateState($a_state) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_PRIV_STATE, $a_state);
	}
	
	
	public function getPrivateFax() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_PRIV_FAX);
	}
	
	public function setPrivateFax($a_fax) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_PRIV_FAX, $a_fax);
	}
	*/
	
	public function getEntryDate() {
		$val = $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_ENTRY_DATE);
		if (!trim($val)) {
			return null;
		}
		try {
			return new ilDate($val, IL_CAL_DATE);
		}
		catch (Exception $e) {
			return null;
		}
	}
	
	public function setEntryDate(ilDate $a_date) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_ENTRY_DATE, $a_date->get(IL_CAL_DATE));
	}
	
	public function getExitDate() {
		$val = $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_EXIT_DATE);
		if (!trim($val)) {
			return null;
		}

		try {
			return new ilDate($val, IL_CAL_DATE);
		}
		catch (Exception $e) {
			return null;
		}
	}

	public function isExitDatePassed() {
		$now = date("Y-m-d");
		$exit_date = $this->getExitDate();

		if(!$exit_date) {
			return false;
		}

		if($now > $exit_date) {
			return true;
		}

		return false;
	}
	
	public function setExitDate(ilDate $a_date) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_EXIT_DATE, $a_date->get(IL_CAL_DATE));
	}
	
	public function getStatus() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_STATUS);
	}
	
	public function setStatus($a_status) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_STATUS, $a_status);
	}
	
	public function getHPE() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_HPE);
	}
	
	public function setHPE($a_hpe) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_HPE, $a_hpe);
	}


	public function getFormattedContactInfo() {
		$name = $this->getFullName();
		$phone = $this->getUser()->getPhoneOffice();
		$email = $this->getEmail();
		
		if (!$phone && !$email) {
			return $name;
		}
		
		if ($phone) {
			if ($email) {
				return $name." ($phone, $email)";
			}
			return $name." ($phone)";
		}
		return $name." ($email)";
	}

	
	// role assignment
	
	public function assignGlobalRole($a_role_title) {
		require_once("Services/GEV/Utils/classes/class.gevRoleUtils.php");
		gevRoleUtils::getInstance()->assignUserToGlobalRole($this->user_id, $a_role_title);
	}
	
	public function deassignGlobalRole($a_role_title) {
		require_once("Services/GEV/Utils/classes/class.gevRoleUtils.php");
		gevRoleUtils::getInstance()->deassignUserFromGlobalRole($this->user_id, $a_role_title);
	}
	
	public function assignOrgRole($a_org_id, $a_role_title) {
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		$utils = gevOrgUnitUtils::getInstance($a_org_id);
		$utils->assignUser($this->user_id, $a_role_title);
	}
	
	public function deassignOrgRole($a_org_id, $a_role_title) {
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		$utils = gevOrgUnitUtils::getInstance($a_org_id);
		$utils->deassignUser($this->user_id, $a_role_title);
	}
	
	
	public function paysFees() {
		return !$this->hasRoleIn(gevSettings::$NO_PAYMENT_ROLES);
	}
	
	public function paysPrearrival() {
		return !$this->hasRoleIn(gevSettings::$NO_PREARRIVAL_PAYMENT_ROLES);
	}

	public function isVFS() {
		return $this->hasRoleIn(array('VFS'));
	}
	
	public function isExpressUser() {
		return $this->hasRoleIn(array("ExpressUser"));
	}
	
	public function getIDHGBAADStatus() {
		$roles = $this->getGlobalRoles();
		foreach ($roles as $role) {
			$title = ilObject::_lookupTitle($role);
			$status = gevSettings::$IDHGBAAD_STATUS_MAPPING[$title];
			if ($status !== null) {
				return $status;
			}
		}
		return "";
	}

	public function isNA() {
		return $this->hasRoleIn(array("NA"));
	}
	
	public function getNAAdviserUtils() {
		if (!$this->isNA()) {
			throw new Exception("User ".$this->user_id." is no NA.");
		}
		
		require_once("Services/GEV/Utils/classes/class.gevNAUtils.php");
		$adviser_id = gevNAUtils::getInstance()->getAdviserOf($this->user_id);
		if ($adviser_id === null) {
			return null;
		}
		
		return gevUserUtils::getInstance($adviser_id);
	}

	public function isUVGDBV() {
		return $this->hasRoleIn(array("DBV UVG"));
		// TODO: implement this correctly
		//return true;
	}

	public function getOD() {
		if ($this->od !== false) {
			return $this->od;
		}
		
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		
		if (!$this->isNA()) {
			$ous = $tree->getOrgUnitOfUser($this->user_id);
		}
		else {
			require_once("Services/GEV/Utils/classes/class.gevNAUtils.php");
			$ous = $tree->getOrgUnitOfUser(gevNAUtils::getInstance()->getAdviserOf($this->user_id));
		}
		foreach($ous as $ou_ref) {
			while ($ou_ref !== null) {
				$ou_id = ilObject::_lookupObjectId($ou_ref);
				$title = ilObject::_lookupTitle($ou_id);
				if (preg_match("/(Organisationsdirektion|OD).*/", $title)) {
					$this->od = array( "obj_id" => $ou_id
									 , "title" => $title
									 );
					return $this->od;
				}
				$ou_ref = $tree->getParent($ou_ref);
			}
		}
		
		$this->od = null;
		return $this->od;
	}

	// Soll für den Benutzer  bei der Selbstbuchung der Hinweis "Vorabendanreise 
	// mit Führungskraft klären" angezeigt werden?
	public function showPrearrivalNoteInBooking() {
		return $this->hasRoleIn(gevUserUtils::$roles_with_prearrival_note);
	}
	
	public function isAdmin() {
		// root
		if ($this->user_id == 6) {
			return true;
		}
		
		return $this->hasRoleIn(gevSettings::$ADMIN_ROLES);
	}
	
	public function getGlobalRoles() {
		return gevRoleUtils::getInstance()->getGlobalRolesOf($this->user_id);
	}
	
	public function hasRoleIn($a_roles) {
		$roles = $this->getGlobalRoles();

		foreach($roles as $key => $value) {
			$roles[$key] = ilObject::_lookupTitle($value);
		}

		foreach ($a_roles as $role) {
			if (in_array($role, $roles)) {
				return true;
			}
		}
		
		return false;
	}
	
	// course specific stuff
	
	public function getFunctionAtCourse($a_crs_id) {
		return gevCourseUtils::getInstance($a_crs_id)->getFunctionOfUser($this->user_id);
	}
	
	public function hasFullfilledPreconditionOf($a_crs_id) {
		return gevCourseUtils::getInstance($a_crs_id)->userFullfilledPrecondition($this->user_id);
	}
	
	public function getOvernightDetailsForCourse(ilObjCourse $a_crs) {
		require_once("Services/Accomodations/classes/class.ilAccomodations.php");
		return ilAccomodations::getInstance($a_crs)
							  ->getAccomodationsOfUser($this->user_id);
	}
	
	public function getFormattedOvernightDetailsForCourse(ilObjCourse $a_crs) {
		return gevGeneralUtils::foldConsecutiveOvernights($this->getOvernightDetailsForCourse($a_crs));
	}
	
	public function getOvernightAmountForCourse(ilObjCourse $a_crs) {
		return count($this->getOvernightDetailsForCourse($a_crs));
	}
	
	public function getUserWhoBookedAtCourse($a_crs_id) {
		require_once("Services/CourseBooking/classes/class.ilCourseBooking.php");
		if (!array_key_exists($a_crs_id, $this->users_who_booked_at_course)) {
			$bk_info = ilCourseBooking::getUserData($a_crs_id, $this->user_id);
			$this->users_who_booked_at_course[$a_crs_id] 
				= new ilObjUser($bk_info["status_changed_by"]);
		}
		
		return $this->users_who_booked_at_course[$a_crs_id];
	}
	
	public function getFirstnameOfUserWhoBookedAtCourse($a_crs_id) {
		return $this->getUserWhoBookedAtCourse($a_crs_id)->getFirstname();
	}
	
	public function getLastnameOfUserWhoBookedAtCourse($a_crs_id) {
		return $this->getUserWhoBookedAtCourse($a_crs_id)->getLastname();
	}
	
	public function getBookingStatusAtCourse($a_course_id) {
		return gevCourseUtils::getInstance($a_course_id)->getBookingStatusOf($this->user_id);
	}
	
	public function getBookedCourses() {
		return $this->courseBookings->getBookedCourses();
	}
	
	public function getWaitingCourses() {
		return $this->courseBookings->getWaitingCourses();
	}
	
	public function getBookedAndWaitingCourses() {
		return array_merge($this->getBookedCourses(), $this->getWaitingCourses());
	}
	
	public function canBookCourseDerivedFromTemplate($a_tmplt_ref_id) {
		if ($a_tmplt_ref_id == 0) {
			return true;
		}
		
		require_once("Services/GEV/Utils/classes/class.gevSettings.php");
		require_once("Services/CourseBooking/classes/class.ilCourseBooking.php");
		require_once("Services/ParticipationStatus/classes/class.ilParticipationStatus.php");
		$field_id = gevSettings::getInstance()->getAMDFieldId(gevSettings::CRS_AMD_TEMPLATE_REF_ID);
		
		$sql =  "SELECT COUNT(*) cnt "
			   ."  FROM adv_md_values_int amd "
			   ."  JOIN crs_book cb ON cb.crs_id = amd.obj_id AND cb.user_id = ".$this->db->quote($this->user_id, "integer")
			   ."  LEFT JOIN crs_pstatus_usr ps ON ps.crs_id = amd.obj_id AND ps.user_id = ".$this->db->quote($this->user_id, "integer")
			   ." WHERE amd.field_id = ".$this->db->quote($field_id, "integer")
			   ."   AND amd.value = ".$this->db->quote($a_tmplt_ref_id, "integer")
			   ."   
			   		AND (    ".$this->db->in("cb.status"
			   								, array(ilCourseBooking::STATUS_BOOKED, ilCourseBooking::STATUS_WAITING)
			   								, false, "integer")
			   ."          AND ( ps.status = ".$this->db->quote(ilParticipationStatus::STATUS_NOT_SET, "integer")
			   ."               OR ps.status IS NULL"
			   ."              )"
			   ."       )"
			   ;
		
		$res = $this->db->query($sql);
		if ($rec = $this->db->fetchAssoc($res)) {
			return $rec["cnt"] == 0;
		}
	
		return true;
	}
	
	// For IV-Import Process
	
	public function iv_isActivated() {
		global $ilDB;
		$res = $this->db->query("SELECT * FROM gev_user_reg_tokens ".
								" WHERE username = ".$ilDB->quote($this->getLogin(), "text").
								"   AND password_changed IS NULL");

		if ($this->db->fetchAssoc($res)) {
			return false;
		}
		return true;
	}
	
	public function iv_setActivated() {
		$this->db->manipulate("UPDATE gev_user_reg_tokens ".
							  "   SET password_changed = NOW() ".
							  " WHERE username = ".$this->db->quote($this->getLogin(), "text")
							  );
	}
	
	// superiors/employees
	
	public function isSuperior() {
		return count($this->getOrgUnitsWhereUserIsDirectSuperior()) > 0;
	}
	
	public function isSuperiorOf($a_user_id) {
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		return in_array($this->user_id, gevOrgUnitUtils::getSuperiorsOfUser($a_user_id));
	}
	
	static public function removeInactiveUsers($a_usr_ids) {
		global $ilDB;
		$res = $ilDB->query("SELECT usr_id "

						   ."  FROM usr_data"
						   ." WHERE ".$ilDB->in("usr_id", $a_usr_ids, false, "integer")
						   ."   AND active = 1"
						   );
		$ret = array();
		while($rec = $ilDB->fetchAssoc($res)) {
			$ret[] = $rec["usr_id"];
		}
		return $ret;
	}
	
	public function getDirectSuperiors() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();

		// This starts with all the org units the user is member in.
		// During the loop we might fill this array with more org units
		// if we could not find any superiors for the user in them.
		$orgus = array_values($tree->getOrgUnitOfUser($this->user_id));

		if (count($orgus) == 0) {
			return array();
		}

		$the_superiors = array();

		$i = -1;
		$initial_amount = count($orgus);
		// We need to check this on every loop as the amount of orgus might change
		// during looping.
		while ($i < count($orgus)) {
			$i++;
			$ref_id = $orgus[$i];

			// Reached the top of the tree.
			if (!$ref_id || $ref_id == ROOT_FOLDER_ID) {
				continue;
			}

			$superiors = $tree->getSuperiors($ref_id);
			$user_is_superior = in_array($this->user_id, $superiors);
			$in_initial_orgus = $i < $initial_amount;

			// I always need to go one org unit up if we are in the original
			// orgu and the user is superior there.
			if ( $in_initial_orgus && $user_is_superior) {
				$orgus[] = $tree->getParent($ref_id);
			}

			// Skip the orgu if there are no superiors there.
			if ( count($superiors) == 0
			|| (   $in_initial_orgus
				// This is only about the org units the user actually is a member of
				&& $user_is_superior
				// If a user is an employee and a superior in one orgunit, he
				// actually seem to be his own superior.
				&& !in_array($this->user_id, $tree->getEmployees($ref_id)))
			) {
				$orgus[] = $tree->getParent($ref_id);
				continue;
			}

			$the_superiors[] = $superiors;
		}

		$the_superiors = call_user_func_array("array_merge", $the_superiors);

		return gevUserUtils::removeInactiveUsers(array_unique($the_superiors));
	}
	
	public function isEmployeeOf($a_user_id) {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		// propably faster then checking the employees of this->user
		return in_array($this->user_id, gevUserUtils::getInstance($a_user_id)->getEmployees());
	}
	
	// returns array containing entries with obj_id and ref_id
	public function getOrgUnitsWhereUserIsDirectSuperior() {
		if ($this->direct_superior_ous !== null) {
			return $this->direct_superior_ous;
		}
		
		$like_role = array();
		foreach (gevSettings::$SUPERIOR_ROLES as $role) {
			$like_role[] = "od.title LIKE ".$this->db->quote($role);
		}
		$like_role = implode(" OR ", $like_role);
		
		$res = $this->db->query(
			 "SELECT oref.obj_id, oref.ref_id "
			."  FROM object_reference oref"
			."  JOIN object_data od ON od.type = 'role' AND ( ".$like_role ." )"
			."  JOIN rbac_fa fa ON fa.rol_id = od.obj_id"
			."  JOIN tree tr ON tr.child = fa.parent"
			."  JOIN rbac_ua ua ON ua.rol_id = od.obj_id"
			."  JOIN object_data od2 ON od2.obj_id = oref.obj_id"
			." WHERE oref.ref_id = tr.parent"
			."   AND oref.deleted IS NULL"
			."   AND ua.usr_id = ".$this->db->quote($this->user_id, "integer")
			."   AND od2.type = 'orgu'"
			);
		$this->direct_superior_ous = array();
		while($rec = $this->db->fetchAssoc($res)) {
			$this->direct_superior_ous[] = array( "obj_id" => $rec["obj_id"]
												, "ref_id" => $rec["ref_id"]
												);
		}
		return $this->direct_superior_ous;
	}
	
	public function getOrgUnitsWhereUserIsSuperior() {
		if ($this->superior_ous !== null) {
			return $this->superior_ous;
		}

		$_ds_ous = $this->getOrgUnitsWhereUserIsDirectSuperior();
		$where = array(" 0 = 1 ");
		$ds_ous = array();
		
		foreach ($_ds_ous as $ou) {
			$where[] = " tr.child = ".$this->db->quote($ou["ref_id"], "integer");
			$ds_ous[] = $ou["ref_id"];
		}
		
		$lr_res = $this->db->query("SELECT lft, rgt FROM tree WHERE ".$this->db->in("child", $ds_ous, false, "integer"));
		
		while ($lr_rec = $this->db->fetchAssoc($lr_res)) {
			$where[] = "(tr.lft > ".$this->db->quote($lr_rec["lft"])." AND tr.rgt < ".$this->db->quote($lr_rec["rgt"]).")";
		}
		$where = implode(" OR ", $where);
		
		$res = $this->db->query(
			 "SELECT DISTINCT oref.ref_id, oref.obj_id "
			."  FROM object_reference oref"
			."  JOIN object_data od ON od.obj_id = oref.obj_id"
			."  JOIN tree tr ON ( ".$where." )"
			." WHERE od.type = 'orgu'"
			."   AND oref.ref_id = tr.child"
			."   AND oref.deleted IS NULL"
			);
		
		$this->superior_ous = array();
		while ($rec = $this->db->fetchAssoc($res)) {
			$this->superior_ous[] = array( "ref_id" => $rec["ref_id"]
										 , "obj_id" => $rec["obj_id"]
										 );
		}
		
		return $this->superior_ous;
	}
	
	public function getOrgUnitNamesWhereUserIsSuperior() {
		if ($this->superior_ou_names !== null) {
			return $this->superior_ou_names;
		}
		
		$ids = $this->getOrgUnitsWhereUserIsSuperior();
		foreach($ids as $key => $value) {
			$ids[$key] = $ids[$key]["obj_id"];
		}
		
		$res = $this->db->query( "SELECT title FROM object_data"
								." WHERE ".$this->db->in("obj_id", $ids, false, "integer")
								." ORDER BY title ASC"
								);
		$this->superior_ou_names = array();
		while ($rec = $this->db->fetchAssoc($res)) {
			$this->superior_ou_names[] = $rec["title"];
		}
		
		return $this->superior_ou_names;
	}

	public function getOrgUnitNamesWhereUserIsDirectSuperior() {
		if ($this->direct_superior_ou_names !== null) {
			return $this->direct_superior_ou_names;
		}
		
		$ids = $this->getOrgUnitsWhereUserIsDirectSuperior();
		foreach($ids as $key => $value) {
			$ids[$key] = $ids[$key]["obj_id"];
		}
		
		$res = $this->db->query( "SELECT title FROM object_data"
								." WHERE ".$this->db->in("obj_id", $ids, false, "integer")
								." ORDER BY title ASC"
								);
		$this->direct_superior_ou_names = array();
		while ($rec = $this->db->fetchAssoc($res)) {
			$this->direct_superior_ou_names[] = $rec["title"];
		}
		
		return $this->direct_superior_ou_names;
	}

	public function getOrgUnitsWhereUserIsEmployee() {
		if ($this->employee_ous !== null) {
			return $this->employee_ous;
		}
		
		$like_role = array();
		foreach (gevSettings::$EMPLOYEE_ROLES as $role) {
			$like_role[] = "od.title LIKE ".$this->db->quote($role);
		}
		$like_role = implode(" OR ", $like_role);

		$res = $this->db->query(
			 "SELECT oref.obj_id, oref.ref_id "
			."  FROM object_reference oref"
			."  JOIN object_data od ON od.type = 'role' AND ( ".$like_role ." )"
			."  JOIN rbac_fa fa ON fa.rol_id = od.obj_id"
			."  JOIN tree tr ON tr.child = fa.parent"
			."  JOIN rbac_ua ua ON ua.rol_id = od.obj_id"
			."  JOIN object_data od2 ON od2.obj_id = oref.obj_id"
			." WHERE oref.ref_id = tr.parent"
			."   AND oref.deleted IS NULL"
			."   AND ua.usr_id = ".$this->db->quote($this->user_id, "integer")
			."   AND od2.type = 'orgu'"
			);
		$this->employee_ous = array();
		while($rec = $this->db->fetchAssoc($res)) {
			$this->employee_ous[] = array( "obj_id" => $rec["obj_id"]
												, "ref_id" => $rec["ref_id"]
												);
		}
		return $this->employee_ous;
	}

	public function getOrgUnitNamesWhereUserIsEmployee() {
		if ($this->employee_ou_names !== null) {
			return $this->employee_ou_names;
		}
		
		$ids = $this->getOrgUnitsWhereUserIsEmployee();
		foreach($ids as $key => $value) {
			$ids[$key] = $ids[$key]["obj_id"];
		}
		
		$res = $this->db->query( "SELECT title FROM object_data"
								." WHERE ".$this->db->in("obj_id", $ids, false, "integer")
								." ORDER BY title ASC"
								);
		$this->employee_ou_names = array();
		while ($rec = $this->db->fetchAssoc($res)) {
			$this->employee_ou_names[] = $rec["title"];
		}
		
		return $this->employee_ou_names;
	}

	public function getAllOrgUnitTitlesUserIsMember() {
		$superior_orgus = $this->getOrgUnitNamesWhereUserIsDirectSuperior();
		$employee_orgus = $this->getOrgUnitNamesWhereUserIsEmployee();

		return array_merge($superior_orgus, $employee_orgus);
	}
	
	public function getOrgUnitsWhereUserCanBookEmployees() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		return $tree->getOrgusWhereUserHasPermissionForOperation("book_employees");
	}
	
	public function getOrgUnitsWhereUserCanBookEmployeesRecursive() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		return $tree->getOrgusWhereUserHasPermissionForOperation("book_employees_rcrsv");
	}
	
	public function getOrgUnitsWhereUserCanViewEmployeeBookings() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		return $tree->getOrgusWhereUserHasPermissionForOperation("view_employee_bookings");
	}
	
	public function getOrgUnitsWhereUserCanViewEmployeeBookingsRecursive() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		return $tree->getOrgusWhereUserHasPermissionForOperation("view_employee_bookings_rcrsv");
	}
	
	public function getOrgUnitsWhereUserCanCancelEmployeeBookings() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		return $tree->getOrgusWhereUserHasPermissionForOperation("cancel_employee_bookings");
	}
	
	public function getOrgUnitsWhereUserCanCancelEmployeeBookingsRecursive() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		return $tree->getOrgusWhereUserHasPermissionForOperation("cancel_employee_bookings_rcrsv");
	}
	
	public function canViewEmployeeBookings() {
		return count($this->getOrgUnitsWhereUserCanViewEmployeeBookings()) > 0
			|| count($this->getOrgUnitsWhereUserCanViewEmployeeBookingsRecursive()) > 0;
	}
	
	public function canCancelEmployeeBookings() {
		return count($this->getOrgUnitsWhereUserCanCancelEmployeeBookings()) > 0
			|| count($this->getOrgUnitsWhereUserCanCancelEmployeeBookingsRecursive()) > 0;
	}

	public function getOrgUnitsWhereUserCanViewEduBios() {
		if ($this->edu_bio_ou_ref_ids) {
			return $this->edu_bio_ou_ref_ids;
		}
		
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		$d = $tree->getOrgusWhereUserHasPermissionForOperation("view_learning_progress");
		$r = $tree->getOrgusWhereUserHasPermissionForOperation("view_learning_progress_rec");
		$rs = array_map(function($v) { return $v["ref_id"]; }, gevOrgUnitUtils::getAllChildren($r));
		$ous = array_unique(array_merge($d, $r, $rs));
		
		$this->edu_bio_ou_ref_ids_all = $rs;
		$this->edu_bio_ou_ref_ids_empl = array_unique(array_merge($d, $r));
		
		$this->edu_bio_ou_ref_ids = $ous;
		return $ous;
	}

	public function getOrgUnitNamesWhereUserCanViewEduBios() {
		if ($this->edu_bio_ou_names !== null) {
			return $this->edu_bio_ou_names;
		}
		
		$ids = $this->getOrgUnitsWhereUserCanViewEduBios();
		$res = $this->db->query( "SELECT title FROM object_data od "
								."  JOIN object_reference oref ON od.obj_id = oref.obj_id"
								." WHERE ".$this->db->in("oref.ref_id", $ids, false, "integer")
								);
		$this->edu_bio_ou_names = array();
		while ($rec = $this->db->fetchAssoc($res)) {
			$this->edu_bio_ou_names[] = $rec["title"];
		}
		
		return $this->edu_bio_ou_names;
	}
	
	public function getEmployeesWhereUserCanViewEduBios() {
		if ($this->edu_bio_usr_ids 	!== null) {
			return $this->edu_bio_usr_ids;
		}
		
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		$this->getOrgUnitsWhereUserCanViewEduBios();
		$e = gevOrgUnitUtils::getEmployeesIn($this->edu_bio_ou_ref_ids_empl);
		$a = gevOrgUnitUtils::getAllPeopleIn($this->edu_bio_ou_ref_ids_all);
		
		$this->edu_bio_usr_ids = array_unique(array_merge($e, $a));
		
		return $this->edu_bio_usr_ids;
	}
	
	public function getEmployees($include_inactive = false) {
		if ($this->employees_active !== null && !$include_inactive) {
			return $this->employees_active;
		}
		if ($this->employees_all !== null && $include_inactive) {
			return $this->employees_all;
		}
		
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");

		$_ds_ous = $this->getOrgUnitsWhereUserIsDirectSuperior();
		$_s_ous = $this->getOrgUnitsWhereUserIsSuperior();
	
		// ref_ids of ous where user is direct superior
		$ds_ous = array();
		foreach($_ds_ous as $ou) {
			$ds_ous[] = $ou["ref_id"];
		}
		// ref_ids of ous where user is superior
		$s_ous = array();
		foreach($_s_ous as $ou) {
			$s_ous[] = $ou["ref_id"];
		}
		
		// ref_ids of ous where user is superior but not direct superior
		$nds_ous = array_diff($s_ous, $ds_ous);
		
		$de = gevOrgUnitUtils::getEmployeesIn($ds_ous);
		$re = gevOrgUnitUtils::getAllPeopleIn($nds_ous);
		
		if (!$include_inactive) {
			$this->employees_active = gevUserUtils::removeInactiveUsers(array_unique(array_merge($de, $re)));
			return $this->employees_active;
		}
		else {
			$this->employees_all = array_unique(array_merge($de, $re));
			return $this->employees_all;
		}
		
	}
	
	public function getVenuesWhereUserIsMember() {
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		$ou_tree = ilObjOrgUnitTree::_getInstance();
		$ous = $ou_tree->getOrgUnitOfUser($this->user_id, 0, true);
		$ret = array();
		foreach ($ous as $ou_id) {
			$utils = gevOrgUnitUtils::getInstance($ou_id);
			if (!$utils->isVenue()) {
				continue;
			}
			$ret[] = $ou_id;
		}
		return $ret;
	}
	
	// billing info
	
	public function getLastBillingDataMaybe() {
		$res = $this->db->query( "SELECT bill_recipient_name, bill_recipient_street, bill_recipient_zip"
								."     , bill_recipient_hnr, bill_recipient_city, bill_recipient_email, bill_cost_center "
								."  FROM bill "
								." WHERE bill_usr_id = ".$this->db->quote($this->user_id, "integer")
								." ORDER BY bill_pk DESC LIMIT 1"
								);
		
		if ($rec = $this->db->fetchAssoc($res)) {
			$spl = explode(",", $rec["bill_recipient_name"]);
			return array( "recipient" => trim($spl[1])
						, "agency" => trim($spl[0])
						, "street" => $rec["bill_recipient_street"]
						, "housenumber" => $rec["bill_recipient_hnr"]
						, "zipcode" => $rec["bill_recipient_zip"]
						, "city" => $rec["bill_recipient_city"]
						, "costcenter" => $rec["bill_cost_center"]
						, "email" => $rec["bill_recipient_email"]
						);
		}
		else {
			return null;
		}
	}



	public function getWBDTPType() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_TP_TYPE);
	}
	
	public function setWBDTPType($a_type) {
		if (!in_array($a_type, array( self::WBD_NO_SERVICE, self::WBD_EDU_PROVIDER
									, self::WBD_TP_BASIS, self::WBD_TP_SERVICE))
			) {
			throw new Exception("gevUserUtils::setWBDTPType: ".$a_type." is no valid type.");
		}

		$this->udf_utils->setField($this->user_id, gevSettings::USR_TP_TYPE, $a_type);
	}

	public function getNextWBDAction() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_NEXT_ACTION);
	}

	public function setNextWBDAction($action) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_WBD_NEXT_ACTION, $action);
	}
	
	public function getWBDBWVId() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_BWV_ID);
	}
	
	public function setWBDBWVId($a_id) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_BWV_ID, $a_id);
	}
	
	public function setTPServiceOld($tp_service_old) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_WBD_TP_SERVICE_OLD, $tp_service_old);
	}

	public function getTPServiceOld() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_TP_SERVICE_OLD);
	}

	public function getRawWBDOKZ() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_OKZ);
	}
	
	public function setRawWBDOKZ($a_okz) {
		if (!in_array($a_okz, array( self::WBD_OKZ_FROM_POSITION, self::WBD_NO_OKZ
								   , self::WBD_OKZ1, self::WBD_OKZ2, self::WBD_OKZ3))
		   ) {
			throw new Exception("gevUserUtils::setRawWBDOKZ: ".$a_okz." is no valid okz.");
		}
		
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_OKZ, $a_okz);
	}
	
	public function getWBDOKZ() {
		$okz = $this->getRawWBDOKZ();
		
		if ($okz == WBD_NO_OKZ) {
			return null;
		}
		
		if (in_array($okz, array(self::WBD_OKZ1, self::WBD_OKZ2, self::WBD_OKZ3))) {
			$spl = explode("-", $okz);
			return trim($spl[1]);
		}
		
		
		// Everyone who has a wbd relevant role also has okz1
		if ($this->hasWBDRelevantRole()) {
			return "OKZ1";
		}
		
		return;
	}
	


	public function getRawWBDAgentStatus() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_STATUS);
	}

	public function getWBDAgentStatus() {
		$agent_status_user =  $this->getRawWBDAgentStatus();

		if(  $agent_status_user == self::WBD_AGENTSTATUS0
		  // When user gets created and nobody clicked "save" on his profile, the
		  // udf-field will not contain a value, thus getRawWBDAgentStatus returned null.
		  // The default for the agent status is to determine it based on the role of
		  // a user.
		  || $agent_status_user === null)
		{
			//0 - aus Stellung	//0 - aus Rolle
			require_once("Services/GEV/Utils/classes/class.gevRoleUtils.php");
			$roles = $this->getGlobalRoles();
			foreach($roles as $key => $value) {
				$roles[$key] = ilObject::_lookupTitle($value);
			}
		
			foreach (self::$wbd_agent_status_mapping as $agent_status => $relevant_roles) {
				foreach ($roles as $role) {
					if(in_array($role, $relevant_roles)){
						$ret = explode("-", $agent_status);
						return trim($ret[1]);
					}
				}
			}
			
			return null;
		}
		$ret = explode("-", $agent_status_user);
		return trim($ret[1]);
	}
	
	public function setRawWBDAgentStatus($a_state) {
	
		if (!in_array($a_state, array( self::WBD_AGENTSTATUS0,
									   self::WBD_AGENTSTATUS1,
									   self::WBD_AGENTSTATUS2,
									   self::WBD_AGENTSTATUS3,
									   self::WBD_AGENTSTATUS4,
									   self::WBD_AGENTSTATUS5,
									   self::WBD_AGENTSTATUS6,
									   self::WBD_AGENTSTATUS7,
									   )
				)
			) {
			throw new Exception("gevUserUtils::setWBDAgentStatus: ".$a_state." is no valid agent status.");
		}
		
		return $this->udf_utils->setField($this->user_id, gevSettings::USR_WBD_STATUS, $a_state);
	}
	
	static public function isValidBWVId($a_id) {
		return 1 == preg_match("/\d{8}-.{6}-../", $a_id);
	}
	
	public function transferPointsToWBD() {
		return (   in_array($this->getWBDOKZ(), 
							array("OKZ1", "OKZ2", "OKZ3"))
				&& in_array($this->getWBDTPType(), 
							array(self::WBD_EDU_PROVIDER, self::WBD_TP_BASIS, self::WBD_TP_SERVICE))
				&& $this->getWBDBWVId()
				);
	}
	
	public function transferPointsFromWBD() {
		return (   in_array($this->getWBDOKZ(), 
							array("OKZ1", "OKZ2", "OKZ3"))
				&& $this->getWBDTPType() == self::WBD_TP_SERVICE
				&& $this->getWBDBWVId()
				);
	}
	
	public function wbdRegistrationIsPending() {
		return (   in_array($this->getWBDOKZ(), 
							array("OKZ1", "OKZ2", "OKZ3"))
				&& in_array($this->getWBDTPType(),
							array(self::WBD_TP_SERVICE, self::WBD_TP_BASIS)
							)
				);	
	}
	
	public function getWBDFirstCertificationPeriodBegin() {
		$date = $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_CERT_PERIOD_BEGIN);
		return new ilDate($date, IL_CAL_DATE);
	}
	
	public function setWBDFirstCertificationPeriodBegin(ilDate $a_start) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_WBD_CERT_PERIOD_BEGIN, $a_start->get(IL_CAL_DATE));
	}
	
	public function getStartOfCurrentCertificationPeriod() {
		return $this->getStartOfCurrentCertificationX(5);
	}
	
	public function getStartOfCurrentCertificationYear() {
		return $this->getStartOfCurrentCertificationX(1);
	}
	
	protected function getStartOfCurrentCertificationX($a_year_step) {
		require_once("Services/Calendar/classes/class.ilDateTime.php");
		
		$now = new ilDate(date("Y-m-d"), IL_CAL_DATE);
		$start = $this->getWBDFirstCertificationPeriodBegin();
		while(   ilDateTime::_before($start, $now)
			  && !ilDateTime::_equals($start, $now)) {
			$start->increment(ilDateTime::YEAR, $a_year_step);
		}
		if (!ilDateTime::_equals($start, $now)) {
			$start->increment(ilDateTime::YEAR, -1 * $a_year_step);
		}
		
		return $start;
	}
	
	public function hasWBDRelevantRole() {
		$query = "SELECT COUNT(*) cnt "
				."  FROM rbac_ua ua "
				."  JOIN object_data od ON od.obj_id = ua.rol_id "
				." WHERE ua.usr_id = ".$this->db->quote($this->user_id, "integer")
				."   AND od.type = 'role' "
				."   AND ".$this->db->in("od.title", self::$wbd_relevant_roles, false, "text")
				;

		$res = $this->db->query($query);
		if ($rec = $this->db->fetchAssoc($res)) {
			return $rec["cnt"] > 0;
		}
		return false;
	}
	
	public function hasDoneWBDRegistration() {
		return ($this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_DID_REGISTRATION) == "1 - Ja");
	}
	
	public function setWBDRegistrationDone() {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_WBD_DID_REGISTRATION, "1 - Ja");
	}
	public function setWBDRegistrationNotDone() {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_WBD_DID_REGISTRATION, "0 - Nein");
	}
	
	public function canBeRegisteredAsTPService() {
		$query = "SELECT COUNT(*) cnt "
				."  FROM rbac_ua ua "
				."  JOIN object_data od ON od.obj_id = ua.rol_id "
				." WHERE ua.usr_id = ".$this->db->quote($this->user_id, "integer")
				."   AND od.type = 'role' "
				."   AND ".$this->db->in("od.title", self::$wbd_tp_service_roles, false, "text")
				;

		$res = $this->db->query($query);
		if ($rec = $this->db->fetchAssoc($res)) {
			return $rec["cnt"] > 0;
		}
		return false;
	}
	
	public function getWBDCommunicationEmail() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_COM_EMAIL);
	}
	
	public function setWBDCommunicationEmail($a_email) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_WBD_COM_EMAIL, $a_email);
	}
	
	public function getExitDateWBD() {
		$date = $this->udf_utils->getField($this->user_id, gevSettings::USR_WBD_EXIT_DATE);
		if (!trim($date)) {
			return null;
		}

		return new ilDate($date, IL_CAL_DATE);
	}
	
	static $hist_position_keys = null;

	static function getPositionKeysFromHisto() {
		if (self::$hist_position_keys !== null) {
			return self::$hist_position_keys;
		}

		global $ilDB;

		$res = $ilDB->query("SELECT DISTINCT position_key FROM hist_user WHERE position_key != '-empty-'");
		self::$hist_position_keys = array();
		while ($rec = $ilDB->fetchAssoc($res)) {
			self::$hist_position_keys[] = $rec["position_key"];
		}
		return self::$hist_position_keys;
	}



	public function getPaisyNr() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_PAISY_NUMBER);
	}
	public function setPaisyNr($a_nr) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_PAISY_NUMBER, $a_nr);
	}
	
	public function getFinancialAccountVFS() {
		return $this->udf_utils->getField($this->user_id, gevSettings::USR_UDF_FINANCIAL_ACCOUNT);
	}
	public function setFinancialAccountVFS($a_nr) {
		$this->udf_utils->setField($this->user_id, gevSettings::USR_UDF_FINANCIAL_ACCOUNT, $a_nr);
	}
	
	static public function userIsInactive($a_user_id) {
		global $ilDB;
		$res = $ilDB->query("SELECT active FROM usr_data"
						   ." WHERE usr_id = ".$ilDB->quote($a_user_id, "integer"));
		
		if ($rec = $ilDB->fetchAssoc($res)) {
			return $rec["active"] != 1;
		}
		
		return false;
	}

	public function getUVGBDOrCPoolNames() {
		$names = array();
		$dbv_utils = gevDBVUtils::getInstance();
		foreach ($dbv_utils->getUVGOrgUnitObjIdsIOf($this->getId()) as $obj_id) {
			$uvg_top_level_orgu_obj_id = $dbv_utils->getUVGTopLevelOrguIdFor($obj_id);
			$names[] = ilObject::_lookupTitle($uvg_top_level_orgu_obj_id);
		}
		return $names;
	}

	/*
	* Gets the user data for report SuperiorWeeklyAction
	*
	* @return array
	*/
	public function getUserDataForSuperiorWeeklyReport($a_start_ts, $a_end_ts) {
		$booking_status = array("gebucht" => "gebucht"
						,"kostenfrei_storniert" => "kostenfrei storniert"
						,"kostenpflichtig_storniert" => "kostenpflichtig storniert"
//						,"auf_warteliste" => "auf Warteliste"
						,"fehlt_ohne_absage" => "fehlt ohne Absage"
						);

		$actions = array(); 
 		$actions["gebucht"] = array();
		$actions["kostenfrei_storniert"] = array();
		$actions["kostenpflichtig_storniert"] = array();
//		$actions["auf_Warteliste"] = array();
		$actions["teilgenommen"] = array();
		$actions["fehlt_ohne_Absage"] = array();

		require_once("Services/GEV/Utils/classes/class.gevOrgUnitUtils.php");
		require_once("Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php");
		$tree = ilObjOrgUnitTree::_getInstance();
		$org_units = $this->getOrgUnitsWhereUserIsDirectSuperior();
		$has_view_empl_perm_ref_ids = $this->getOrgUnitsWhereUserCanViewEmployeeBookings();
		$ref_ids = array();
		$ref_id_child_orgunit = array();
		
		foreach ($org_units as $org_unit) {
			// Only take the org units where the user is superior and also has the permission
			// to view bookings of employees.
			if (!in_array($has_view_empl_perm_ref_ids, $org_unit["ref_id"])) {
				continue;
			}

			$ref_ids[] = $org_unit["ref_id"];
			$org_util = gevOrgUnitUtils::getInstance($org_unit["obj_id"]);
			foreach($org_util->getOrgUnitsOneTreeLevelBelow() as $org_unit_child) {
				$ref_id_child_orgunit[] = $org_unit_child["ref_id"];
			}
		}

		$empl = array();
		$sup = array();

		if(!empty($ref_ids)) {
			$empl = gevOrgUnitUtils::getEmployeesIn($ref_ids);
		}

		if(!empty($ref_id_child_orgunit)) {
			$sup = gevOrgUnitUtils::getSuperiorsIn($ref_id_child_orgunit);
		}

		if(!empty($empl) || !empty($sup)) {
			$to_search = array_merge($empl,$sup);

			$sql_emp = "SELECT DISTINCT" 
					." histc.crs_id, histc.begin_date, histc.end_date, histucs.overnights, histucs.booking_status,"
					." histucs.participation_status, histu.firstname, histu.lastname, histc.title, histc.type, histc.edu_program, "
					." IF(crsa_start.night IS NULL, false, true) AS prearrival,"
					." IF(crsa_end.night IS NULL, false, true) AS postdeparture"
					." FROM hist_usercoursestatus histucs"
					." JOIN hist_user histu ON histu.user_id = histucs.usr_id AND histu.hist_historic = 0"
					." JOIN hist_course histc ON histc.crs_id = histucs.crs_id AND histc.hist_historic = 0"
					." LEFT JOIN crs_acco crsa_start ON crsa_start.user_id = histu.user_id AND crsa_start.crs_id = histc.crs_id AND crsa_start.night = DATE_SUB(histucs.begin_date, INTERVAL 1 DAY)"
					." LEFT JOIN crs_acco crsa_end ON crsa_start.user_id = histu.user_id AND crsa_start.crs_id = histc.crs_id AND crsa_end.night = histucs.end_date"
					." WHERE histucs.created_ts BETWEEN ".$this->db->quote($a_start_ts, "integer")." AND ".$this->db->quote($a_end_ts, "integer").""
					." AND ".$this->db->in("histucs.booking_status", $booking_status, false, "text").""
					." AND histucs.hist_historic = 0"
					." AND ".$this->db->in("histu.user_id", $to_search, false, "integer").""
					." ORDER BY histucs.booking_status, histu.lastname, histu.firstname, histucs.created_ts";

			$res_emp = $this->db->query($sql_emp);

			while($row_emp = $this->db->fetchAssoc($res_emp)) {
				switch($row_emp["booking_status"]) {
					case "gebucht":
						if($row_emp["participation_status"] == "teilgenommen") {
							$actions["teilgenommen"][] = $row_emp;
							break;
						}

						if($row_emp["participation_status"] == "fehlt ohne Absage") {
							$actions["fehlt_ohne_Absage"][] = $row_emp;
							break;
						}

						$actions["gebucht"][] = $row_emp;
						break;
					case "kostenfrei storniert":
						$actions["kostenfrei_storniert"][] = $row_emp;
						break;
					case "kostenpflichtig storniert":
						$actions["kostenpflichtig_storniert"][] = $row_emp;
						break;
/*					case "auf Warteliste":
						$actions["auf_Warteliste"][] = $row_emp;
						break;*/
					default:
						break;
				}
			}
 		}

	 	return $actions;
	}

	public function seeBiproAgent() {
		$roles = array("Administrator"
					   ,"Admin-Voll"
					   ,"Admin-eingeschraenkt"
					   ,"Admin-Ansicht"
					   ,"OD/BD"
					   ,"FD"
					   ,"UA"
					   ,"HA 84"
					   ,"BA 84"
					   ,"Org PV 59"
					   ,"PV 59"
					   ,"AVL"
					   ,"ID FK"
					   ,"ID MA"
					   ,"OD/FD/BD ID"
					   ,"Agt-ID"
					   ,"NFK"
					   ,"FDA"
					   ,"int. Trainer"
					   ,"OD-Betreuer"
					   ,"DBV UVG"
					   ,"DBV EVG"
					   ,"DBV-Fin-UVG"
					   ,"RTL"
					);

		return $this->hasRoleIn($roles);
	}

	public function seeBiproSuperior() {
		$roles = array("Administrator"
					   ,"Admin-Voll"
					   ,"Admin-eingeschraenkt"
					   ,"Admin-Ansicht"
					   ,"OD/BD"
					   ,"FD"
					   ,"UA"
					   ,"AVL"
					   ,"ID FK"
					   ,"NFK"
					   ,"FDA"
					   ,"int. Trainer"
					   ,"OD-Betreuer"
					   ,"RTL"
					);
		
		return $this->hasRoleIn($roles);
	}

	/**
	* Checks requirements user must have to get in pool for new wbd account
	*
	* WBD Resistration done
	* has specified Role
	* is an existing user
	* is aktive
	* is not root oder anomynos
	* entry date is passed
	* has no BWV Id
	* has specifed TP-Types
	*
	* @return boolean
	*/
	public function wbdShouldBeRegisteredAsNew() {
		return $this->hasDoneWBDRegistration() && $this->hasWBDRelevantRole() && $this->userExists() && $this->isActive() && !$this->hasSpecialUserId()
				&& $this->entryDatePassed() && $this->isWBDBWVIdEmpty() && $this->hasWBDType(self::WBD_NO_SERVICE);
	}

	/**
	* Checks requirements user must have to get in pool for affiliate as TP-Service
	*
	* WBD Resistration done
	* has specified Role
	* is an existing user
	* is aktive
	* is not root oder anomynos
	* entry date is passed
	* has BWV Id
	* has not specifed TP-Type
	* has no open specified errors
	*
	* @return boolean
	*/
	public function wbdShouldBeAffiliateAsTPService() {
		$wbd_errors = array(WBD_ERROR_WRONG_USERDATA
							, WBD_ERROR_USER_SERVICETYPE
							, WBD_ERROR_USER_DIFFERENT_TP
							, WBD_ERROR_USER_UNKNOWN
							, WBD_ERROR_USER_DEACTIVATED);

		return $this->hasDoneWBDRegistration() && $this->hasWBDRelevantRole() && $this->userExists() && $this->isActive() && !$this->hasSpecialUserId()
				&& $this->entryDatePassed() && !$this->isWBDBWVIdEmpty() && !$this->hasWBDType(self::WBD_TP_SERVICE) 
				&& !$this->hasOpenWBDErrors($wbd_errors);
	}

	/**
	* Checks requirements user must have to get in pool for release
	*
	* is an existing user
	* is not root oder anomynos
	* exit date is passed
	* has no wbd exit date
	* has specifed TP-Type
	* has BWV Id
	* has no open specified errors
	*
	* @return boolean
	*/
	public function wbdShouldBeReleased() {
		$wbd_errors = array(WBD_ERROR_WRONG_USERDATA
							, WBD_ERROR_USER_SERVICETYPE
							, WBD_ERROR_USER_DIFFERENT_TP
							, WBD_ERROR_USER_UNKNOWN
							, WBD_ERROR_USER_DEACTIVATED
							, WBD_ERROR_NO_RELEASE);

		return $this->userExists() && !$this->hasSpecialUserId()
				&& $this->isExitDatePassed() && !$this->hasExitDateWBD() && $this->hasWBDType(self::WBD_TP_SERVICE) && !$this->isWBDBWVIdEmpty()
				&& !$this->hasOpenWBDErrors($wbd_errors);
	}

	/**
	* checks bwvs id empty or not
	*
	* @return boolean
	*/
	protected function isWBDBWVIdEmpty() {
		return $this->getWBDBWVId() === null;
	}

	/**
	* checks user is active
	*
	* @return boolean
	*/
	protected function isActive() {
		return $this->getUser()->getActive();
	}

	/**
	* checks user has one of given wbd tp types
	*
	* @param array
	* @return boolen
	*/
	protected function hasOneWBDTypeOf(array $wbd_types) {
		return in_array($this->getWBDTPType(), $wbd_types);
	}

	/**
	* checks user has specified wbd tp type
	* 
	* @param string
	* @return boolean
	*/
	protected function hasWBDType($wbd_type) {
		return $this->getWBDTPType() == $wbd_type;
	}

	/**
	* checks if entry date is passed or not
	*
	* @return boolean
	*/
	protected function entryDatePassed() {
		$now = date("Y-m-d");
		$entry_date = $this->getEntryDate();

		if(!$entry_date) {
			return false;
		}

		return $entry_date->get(IL_CALC_DATE) <= $now;
	}

	/**
	* checks if user is an real ilias user
	*
	* @return boolean
	*/
	protected function userExists() {
		return ilObjUser::_lookupLogin($this->user_id) !== false;
	}

	static $specialUserIds = array(6,13);
	/**
	* checks user is not root or anomynos or some one else
	* look at array $specialUserIds
	*
	* @return boolean
	*/
	protected function hasSpecialUserId() {
		return in_array($this->user_id, self::$specialUserIds);
	}

	/**
	* checks if there are some open WBD errors according to specified error group
	*
	* @param array
	* @return boolean
	*/
	protected function hasOpenWBDErrors(array $wbd_errors) {
		$sql = "SELECT DISTINCT count(usr_id) as cnt\n"
				." FROM wbd_errors\n"
				." WHERE resolved=0\n"
				."   AND ".$this->db->in("reason", $wbd_errors, false, "text")."\n"
				."   AND usr_id = ".$this->db->quote($this->user_id,"integer")."\n";

		$res = $this->db->query($sql);
		while($row = $this->db->fetchAssoc($res)) {
			return $row["cnt"] > 0;
		}

		return false;
	}

	/**
	* check if there is an WBD Exit date
	*
	* @return boolean
	*/
	protected function hasExitDateWBD() {
		return $this->getExitDateWBD() !== null;
	}

}
