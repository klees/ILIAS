<?php
/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilCourseHistorizingHelper
 * 
 * @author Maximilian Becker <mbecker@databay.de>
 * @author Richard Klees <richard.klees@concepts-and-training.de>
 * @version $Id$
 */


require_once("Services/GEV/Utils/classes/class.gevCourseUtils.php");

class ilCourseHistorizingHelper 
{
	#region Singleton

	/** Defunct member for singleton */
	private function __clone() {}

	/** Defunct member for singleton */
	private function __construct() {}

	/** @var ilCourseHistorizingHelper $instance */
	private static $instance;

	/**
	 * Singleton accessor
	 * 
	 * @static
	 * 
	 * @return ilUserHistorizingHelper
	 */
	public static function getInstance()
	{
		if(!self::$instance)
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	#endregion

	/**
	 * Returns the custom id of the given course.
	 * 
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getCustomIdOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getCustomId();
	}

	/**
	 * Returns the template title of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getTemplateTitleOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getTemplateTitle();
	}

	/**
	 * Returns the type of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getTypeOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getType();
	}

	/**
	 * Returns the topic/s of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return array
	 */
	public static function getTopicOf($course)
	{
		$topic =  gevCourseUtils::getInstanceByObjOrId($course)
								->getTopics();
		if ($topic === null) {
			return array();
		}
		else {
			return $topic;
		}
	}

	/**
	 * Returns the begin of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return ilDate
	 */
	public static function getBeginOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getStartDate();
	}

	/**
	 * Returns the end of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return ilDate
	 */
	public static function getEndOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getEndDate();
	}

	/**
	 * Returns the hours of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return integer
	 */
	public static function getHoursOf($course)
	{
		// count hours in schedule 
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getAmountHours();
	}

	/**
	 * Returns the is_expert_course flag of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return boolean
	 */
	public static function isExpertCourse($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getIsExpertTraining();
	}

	/**
	 * Returns the venue of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getVenueOf($course)
	{
		$venue = gevCourseUtils::getInstanceByObjOrId($course)
							 ->getVenueTitle();
		if ($venue == "") {
			return self::getVenueFreeTextOf($course);
		}

		return $venue;
	}

	/**
	 * Returns the venue free text marker of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getVenueFreeTextOf($course)
	{
		$ret = gevCourseUtils::getInstanceByObjOrId($course)
							 ->getVenueFreeText();

		return ($ret) ? "FREITEXT" : "";
	}

	/**
	 * Returns the provider of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getProviderOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getProviderTitle();
	}

	/**
	 * Returns the max credit points of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getMaxCreditPointsOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getCreditPoints();
	}

	/**
	 * Returns the fee of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getFeeOf($course)
	{
		return gevCourseUtils::getInstanceByObjOrId($course)
							 ->getFee();
	}

	/**
	 * Returns the tutor of the given course.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getTutorOf($course)
	{
		$utils = gevCourseUtils::getInstanceByObjOrId($course);

		$lastname = $utils->getMainTrainerLastname();
		$firstname = $utils->getMainTrainerFirstname();

		if ($lastname && $firstname) {
			return $lastname.", ".$firstname;
		}
	}
	
	/**
	 * Returns weather course is a template object or not.
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */
	public static function getIsTemplate($course) {
		if (gevCourseUtils::getInstanceByObjOrId($course)
						  ->isTemplate()) {
			return "Ja";
		}
		else {
			return "Nein";
		}
	}

	/**
	 * Returns the standardized contents of the course for WBD
	 *
	 * @param integer|ilObjCourse $course
	 *
	 * @return string
	 */

	public static function getWBDTopicOf($course) {
		$utils = gevCourseUtils::getInstanceByObjOrId($course);
		return $utils->getWBDTopic();
	}

	public static function getEduProgramOf($course) {
		$utils = gevCourseUtils::getInstanceByObjOrId($course);
		return $utils->getEduProgramm();
	}

	public static function isOnline($course) {
		return $course->isActivated();
	}

	public static function getDeadlineDataOf($course) {

		require_once("Services/GEV/Mailing/classes/class.gevCrsAdditionalMailSettings.php");
		$mailings = new gevCrsAdditionalMailSettings($course->getId());
		$utils = gevCourseUtils::getInstanceByObjOrId($course);
		//get deadlines for course
		$ret = array(
			'dl_invitation'		=> $mailings->getInvitationMailingDate(),
			'dl_storno'			=> $utils->getCancelDeadline(),
			'dl_booking'		=> $utils->getBookingDeadline(),
			'dl_waitinglist'	=> $utils->getCancelWaitingList()
		);


		return $ret;


	}

	public static function getDBVHotTopicOf($course) {
		$utils = gevCourseUtils::getInstanceByObjOrId($course);
		return $utils->getDBVHotTopic();
	}

	public static function getVirtualClassroomType($course) {
		$utils = gevCourseUtils::getInstanceByObjOrId($course);
		return $utils->getVirtualClassType();
	}

	public static function getDCTType($course) {
		$utils = gevCourseUtils::getInstanceByObjOrId($course);
		if($utils->isDecentralTraining()) {
			return ($utils->isFlexibleDecentrallTraining()) ? 'flexible' : 'fixed';
		}
		return;
	}

	public static function getTemplateObjId($course) {
		$utils = gevCourseUtils::getInstanceByObjOrId($course);
		$template_ref_id = $utils->getTemplateRefId();
		if($template_ref_id) {
			require_once("Services/GEV/Utils/classes/class.gevObjectUtils.php");
			return gevObjectUtils::getObjId($template_ref_id);
		}
		return;
	}

}