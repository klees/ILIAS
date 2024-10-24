<?php declare(strict_types=1);

/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface for ilObject dependency
 * @author  killing@leifos.de
 * @ingroup ServicesConditions
 */
interface ilConditionObjectAdapterInterface
{
    /**
     * Get object id for reference id
     */
    public function getObjIdForRefId(int $a_ref_id) : int;

    /**
     * Get object type for object id
     * @param int $a_obj_id
     * @return string
     */
    public function getTypeForObjId(int $a_obj_id) : string;
}
