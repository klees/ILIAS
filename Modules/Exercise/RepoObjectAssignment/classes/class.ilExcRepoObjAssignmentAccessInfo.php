<?php

/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Repository object assignment information
 *
 * @author Alex Killing <killing@leifos.de>
 */
class ilExcRepoObjAssignmentAccessInfo implements ilExcRepoObjAssignmentAccessInfoInterface
{
    protected bool $is_granted;

    /**
     * @var string[]
     */
    protected array $not_granted_reasons;

    protected ilLanguage $lng;
    protected ilAccessHandler $access;

    /**
     * Constructor
     * @param bool $a_is_granted
     * @param string[] $a_not_granted_reasons
     */
    protected function __construct(bool $a_is_granted, array $a_not_granted_reasons)
    {
        global $DIC;

        $this->access = $DIC->access();
        $this->is_granted = $a_is_granted;
        $this->not_granted_reasons = $a_not_granted_reasons;

        $this->lng = $DIC->language();
    }


    /**
     * Is access granted due to exercise assignment conditions?
     */
    public function isGranted() : bool
    {
        return $this->is_granted;
    }

    /**
     * Get reasons why access is not granted.
     *
     * @return string[]
     */
    public function getNotGrantedReasons() : array
    {
        return $this->not_granted_reasons;
    }

    /**
     * @param int $a_ref_id ref id
     * @param int $a_user_id user id
     * @return self
     */
    public static function getInfo(int $a_ref_id, int $a_user_id) : self
    {
        global $DIC;

        $repo_obj_ass = ilExcRepoObjAssignment::getInstance();
        $lng = $DIC->language();
        $access = $DIC->access();

        // if this object is not assigned to any assignment, we do not deny the access
        $assignment_info = $repo_obj_ass->getAssignmentInfoOfObj($a_ref_id, $a_user_id);
        if (count($assignment_info) == 0) {
            return new self(true, []);
        }

        $granted = true;
        $reasons = [];
        foreach ($assignment_info as $i) {
            if (!$i->isUserSubmission()) {
                $has_write_permission = false;
                foreach ($i->getReadableRefIds() as $exc_ref_id) {
                    if ($access->checkAccessOfUser($a_user_id, "write", "", $exc_ref_id)) {
                        $has_write_permission = true;
                    }
                }
                if (!$has_write_permission) {
                    $granted = false;
                    $reasons[0] = $lng->txt("exc_obj_not_submitted_by_user");
                }
            }
        }

        return new self($granted, $reasons);
    }
}
