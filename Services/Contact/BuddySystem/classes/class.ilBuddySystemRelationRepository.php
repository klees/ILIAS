<?php declare(strict_types=1);
/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilBuddySystemRelationRepository
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilBuddySystemRelationRepository
{
    private const TYPE_APPROVED = 'app';
    private const TYPE_REQUESTED = 'req';
    private const TYPE_IGNORED = 'ign';

    protected ilDBInterface $db;
    protected int $usrId;

    public function __construct(int $usrId)
    {
        global $DIC;

        $this->db = $DIC['ilDB'];
        $this->usrId = $usrId;
    }

    /**
     * Reads all items from database
     * @return ilBuddySystemRelation[]
     */
    public function getAll() : array
    {
        $relations = [];

        $res = $this->db->queryF(
            '
			SELECT
			       buddylist.usr_id, buddylist.buddy_usr_id, buddylist.ts, %s rel_type
            FROM buddylist
			INNER JOIN usr_data ud
                ON ud.usr_id = buddylist.usr_id
			WHERE buddylist.usr_id = %s
			UNION
			SELECT
			       buddylist_requests.usr_id, buddylist_requests.buddy_usr_id, buddylist_requests.ts, (CASE WHEN ignored = 1 THEN %s ELSE %s END) rel_type
			FROM buddylist_requests
			INNER JOIN usr_data ud ON ud.usr_id = buddylist_requests.usr_id
			INNER JOIN usr_data udbuddy ON udbuddy.usr_id = buddylist_requests.buddy_usr_id
			WHERE buddylist_requests.usr_id = %s OR buddylist_requests.buddy_usr_id = %s
			',
            [
                'text',
                'integer',
                'text',
                'text',
                'integer',
                'integer'
            ],
            [
                self::TYPE_APPROVED,
                $this->usrId,
                self::TYPE_IGNORED,
                self::TYPE_REQUESTED,
                $this->usrId,
                $this->usrId
            ]
        );

        while ($row = $this->db->fetchAssoc($res)) {
            $relation = $this->getRelationByDatabaseRecord($row);
            $key = $this->usrId === $relation->getUsrId() ? $relation->getBuddyUsrId() : $relation->getUsrId();
            $relations[$key] = $relation;
        }

        return $relations;
    }

    private function getRelationByDatabaseRecord(array $row) : ilBuddySystemRelation
    {
        if (self::TYPE_APPROVED === $row['rel_type']) {
            return new ilBuddySystemRelation(
                new ilBuddySystemLinkedRelationState(),
                (int) $row['usr_id'],
                (int) $row['buddy_usr_id'],
                $row['usr_id'] === $this->usrId,
                (int) $row['ts']
            );
        } elseif (self::TYPE_IGNORED === $row['rel_type']) {
            return new ilBuddySystemRelation(
                new ilBuddySystemIgnoredRequestRelationState(),
                (int) $row['usr_id'],
                (int) $row['buddy_usr_id'],
                $row['usr_id'] === $this->usrId,
                (int) $row['ts']
            );
        }

        return new ilBuddySystemRelation(
            new ilBuddySystemRequestedRelationState(),
            (int) $row['usr_id'],
            (int) $row['buddy_usr_id'],
            $row['usr_id'] === $this->usrId,
            (int) $row['ts']
        );
    }

    public function destroy() : void
    {
        $this->db->queryF(
            'DELETE FROM buddylist WHERE usr_id = %s OR buddy_usr_id = %s',
            ['integer', 'integer'],
            [$this->usrId, $this->usrId]
        );

        $this->db->queryF(
            'DELETE FROM buddylist_requests WHERE usr_id = %s OR buddy_usr_id = %s',
            ['integer', 'integer'],
            [$this->usrId, $this->usrId]
        );
    }

    private function addToApprovedBuddies(ilBuddySystemRelation $relation) : void
    {
        $this->db->replace(
            'buddylist',
            [
                'usr_id' => ['integer', $relation->getUsrId()],
                'buddy_usr_id' => ['integer', $relation->getBuddyUsrId()]
            ],
            [
                'ts' => ['integer', $relation->getTimestamp()]
            ]
        );

        $this->db->replace(
            'buddylist',
            [
                'usr_id' => ['integer', $relation->getBuddyUsrId()],
                'buddy_usr_id' => ['integer', $relation->getUsrId()]
            ],
            [
                'ts' => ['integer', $relation->getTimestamp()]
            ]
        );
    }

    private function removeFromApprovedBuddies(ilBuddySystemRelation $relation) : void
    {
        $this->db->manipulateF(
            'DELETE FROM buddylist WHERE usr_id = %s AND buddy_usr_id = %s',
            ['integer', 'integer'],
            [$relation->getUsrId(), $relation->getBuddyUsrId()]
        );

        $this->db->manipulateF(
            'DELETE FROM buddylist WHERE buddy_usr_id = %s AND usr_id = %s',
            ['integer', 'integer'],
            [$relation->getUsrId(), $relation->getBuddyUsrId()]
        );
    }

    private function addToRequestedBuddies(ilBuddySystemRelation $relation, bool $ignored) : void
    {
        $this->db->replace(
            'buddylist_requests',
            [
                'usr_id' => ['integer', $relation->getUsrId()],
                'buddy_usr_id' => ['integer', $relation->getBuddyUsrId()]
            ],
            [
                'ts' => ['integer', $relation->getTimestamp()],
                'ignored' => ['integer', (int) $ignored]
            ]
        );
    }

    private function removeFromRequestedBuddies(ilBuddySystemRelation $relation) : void
    {
        $this->db->manipulateF(
            'DELETE FROM buddylist_requests WHERE usr_id = %s AND buddy_usr_id = %s',
            ['integer', 'integer'],
            [$relation->getUsrId(), $relation->getBuddyUsrId()]
        );

        $this->db->manipulateF(
            'DELETE FROM buddylist_requests WHERE buddy_usr_id = %s AND usr_id = %s',
            ['integer', 'integer'],
            [$relation->getUsrId(), $relation->getBuddyUsrId()]
        );
    }

    public function save(ilBuddySystemRelation $relation) : void
    {
        $ilAtomQuery = $this->db->buildAtomQuery();
        $ilAtomQuery->addTableLock('buddylist_requests');
        $ilAtomQuery->addTableLock('buddylist');

        $ilAtomQuery->addQueryCallable(function (ilDBInterface $ilDB) use ($relation) : void {
            if ($relation->isLinked()) {
                $this->addToApprovedBuddies($relation);
            } elseif ($relation->wasLinked()) {
                $this->removeFromApprovedBuddies($relation);
            }

            if ($relation->isRequested()) {
                $this->addToRequestedBuddies($relation, false);
            } elseif ($relation->isIgnored()) {
                $this->addToRequestedBuddies($relation, true);
            } elseif ($relation->wasRequested() || $relation->wasIgnored()) {
                $this->removeFromRequestedBuddies($relation);
            }
        });

        $ilAtomQuery->run();
    }
}
