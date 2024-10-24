<?php declare(strict_types=0);

/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Stefan Meyer <meyer@leifos.com>
 * @version $Id$
 * @package ilias-tracking
 */
class ilLPStatusObjectives extends ilLPStatus
{
    public static function _getNotAttempted(int $a_obj_id) : array
    {
        $users = array();

        $members = self::getMembers($a_obj_id);
        if ($members) {
            // diff in progress, completed and failed (use stored result in LPStatusWrapper)
            $users = array_diff((array) $members, ilLPStatusWrapper::_getInProgress($a_obj_id));
            $users = array_diff((array) $members, ilLPStatusWrapper::_getCompleted($a_obj_id));
            $users = array_diff((array) $members, ilLPStatusWrapper::_getFailed($a_obj_id));
        }
        return $users;
    }

    public static function _getInProgress(int $a_obj_id) : array
    {
        $objective_results = ilLPStatusWrapper::_getStatusInfo($a_obj_id);
        $usr_ids = (array) $objective_results['user_status'][self::LP_STATUS_IN_PROGRESS_NUM];

        if ($usr_ids) {
            // Exclude all non members
            $usr_ids = array_intersect(self::getMembers($a_obj_id), $usr_ids);
        }

        if ($usr_ids) {
            return $usr_ids;
        } else {
            return array();
        }
    }

    public static function _getCompleted(int $a_obj_id) : array
    {
        $objective_results = ilLPStatusWrapper::_getStatusInfo($a_obj_id);
        $usr_ids = (array) $objective_results['user_status'][self::LP_STATUS_COMPLETED_NUM];

        if ($usr_ids) {
            // Exclude all non members
            $usr_ids = array_intersect(self::getMembers($a_obj_id), $usr_ids);
        }

        return $usr_ids ?: array();
    }

    public static function _getFailed(int $a_obj_id) : array
    {
        $objective_results = ilLPStatusWrapper::_getStatusInfo($a_obj_id);
        $usr_ids = (array) $objective_results['user_status'][self::LP_STATUS_FAILED_NUM];

        if ($usr_ids) {
            // Exclude all non members
            $usr_ids = array_intersect(self::getMembers($a_obj_id), $usr_ids);
        }

        return $usr_ids;
    }

    public static function _getStatusInfo(int $a_obj_id) : array
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $status_info = array();
        $status_info['user_status'] = array();
        $status_info['objectives'] = ilCourseObjective::_getObjectiveIds($a_obj_id, true);
        $status_info['num_objectives'] = count($status_info['objectives']);

        if ($status_info['num_objectives']) {
            $in = $ilDB->in('objective_id', $status_info['objectives'], false, 'integer');

            foreach (ilLOUserResults::getSummarizedObjectiveStatusForLP($a_obj_id,
                $status_info['objectives']) as $user_id => $user_status) {
                $status_info['user_status'][$user_status][] = $user_id;
            }

            // change event should lead to "in progress" - see determineStatus()
            foreach (ilChangeEvent::lookupUsersInProgress($a_obj_id) as $user_id) {
                if (!is_array($status_info['user_status'][ilLPStatus::LP_STATUS_IN_PROGRESS_NUM]) ||
                    !in_array($user_id, $status_info['user_status'][ilLPStatus::LP_STATUS_IN_PROGRESS_NUM])) {
                    $status_info['user_status'][ilLPStatus::LP_STATUS_IN_PROGRESS_NUM][] = $user_id;
                }
            }

            // Read title/description
            $query = "SELECT * FROM crs_objectives WHERE " . $in;
            $res = $ilDB->query($query);
            while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
                $status_info['objective_title'][$row->objective_id] = $row->title;
                $status_info['objective_description'][$row->objective_id] = $row->description;
            }
        }
        return $status_info;
    }

    public function determineStatus(int $a_obj_id, int $a_usr_id, object $a_obj = null) : int
    {
        // the status completed depends on:
        // $status_info['num_objectives'] (ilLPStatusWrapper::_getStatusInfo($a_obj_id);)
        // - ilCourseObjective::_getObjectiveIds($a_obj_id);
        // - table crs_objectives manipulated in
        // - ilCourseObjective

        // $status_info['objective_result']  (ilLPStatusWrapper::_getStatusInfo($a_obj_id);)
        // table crs_objective_status (must not contain a dataset)
        // ilCourseObjectiveResult -> added ilLPStatusWrapper::_updateStatus()

        $status = self::LP_STATUS_NOT_ATTEMPTED_NUM;
        switch ($this->ilObjDataCache->lookupType($a_obj_id)) {
            case "crs":
                if (ilChangeEvent::hasAccessed($a_obj_id, $a_usr_id)) {
                    // an initial test (only) should also lead to "in progress"
                    $status = self::LP_STATUS_IN_PROGRESS_NUM;

                    $objectives = ilCourseObjective::_getObjectiveIds($a_obj_id, true);
                    if ($objectives) {
                        // #14051 - getSummarizedObjectiveStatusForLP() might return null
                        $objtv_status = ilLOUserResults::getSummarizedObjectiveStatusForLP($a_obj_id, $objectives,
                            $a_usr_id);
                        if ($objtv_status !== null) {
                            $status = $objtv_status;
                        }
                    }
                }
                break;
        }
        return $status;
    }

    /**
     * @param int $a_obj_id
     * @return int[]
     */
    protected static function getMembers(int $a_obj_id) : array
    {
        $member_obj = ilCourseParticipants::_getInstanceByObjId($a_obj_id);
        return $member_obj->getMembers();
    }

    /**
     * Get completed users for object
     */
    public static function _lookupCompletedForObject(int $a_obj_id, ?array $a_user_ids = null) : array
    {
        if (!$a_user_ids) {
            $a_user_ids = self::getMembers($a_obj_id);
            if (!$a_user_ids) {
                return array();
            }
        }
        return self::_lookupStatusForObject($a_obj_id, self::LP_STATUS_COMPLETED_NUM, $a_user_ids);
    }

    /**
     * Get failed users for object
     */
    public static function _lookupFailedForObject(int $a_obj_id, ?array $a_user_ids = null) : array
    {
        return array();
    }

    /**
     * Get in progress users for object
     */
    public static function _lookupInProgressForObject(int $a_obj_id, ?array $a_user_ids = null) : array
    {
        if (!$a_user_ids) {
            $a_user_ids = self::getMembers($a_obj_id);
            if (!$a_user_ids) {
                return array();
            }
        }
        return self::_lookupStatusForObject($a_obj_id, self::LP_STATUS_IN_PROGRESS_NUM, $a_user_ids);
    }
}
