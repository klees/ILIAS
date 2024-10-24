<?php declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilMailGroupAddressType
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilMailGroupAddressType extends ilBaseMailAddressType
{
    protected function isValid(int $senderId) : bool
    {
        return $this->typeHelper->doesGroupNameExists(substr($this->address->getMailbox(), 1));
    }

    public function resolve() : array
    {
        $usrIds = [];

        $possibleGroupTitle = substr($this->address->getMailbox(), 1);
        $possibleGroupObjId = $this->typeHelper->getGroupObjIdByTitle($possibleGroupTitle);

        $group = null;
        foreach ($this->typeHelper->getAllRefIdsForObjId($possibleGroupObjId) as $refId) {
            $group = $this->typeHelper->getInstanceByRefId($refId);
            break;
        }

        if ($group instanceof ilObjGroup) {
            foreach ($group->getGroupMemberIds() as $usr_id) {
                $usrIds[] = $usr_id;
            }

            $this->logger->debug(sprintf(
                "Found the following group member user ids for address (object title) '%s' and obj_id %s: %s",
                $possibleGroupTitle,
                $possibleGroupObjId,
                implode(', ', array_unique($usrIds))
            ));
        } else {
            $this->logger->debug(sprintf(
                "Did not find any group object for address (object title) '%s'",
                $possibleGroupTitle
            ));
        }

        return array_unique($usrIds);
    }
}
