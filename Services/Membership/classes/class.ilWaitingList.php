<?php declare(strict_types=1);/*
        +-----------------------------------------------------------------------------+
        | ILIAS open source                                                           |
        +-----------------------------------------------------------------------------+
        | Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
        |                                                                             |
        | This program is free software; you can redistribute it and/or               |
        | modify it under the terms of the GNU General Public License                 |
        | as published by the Free Software Foundation; either version 2              |
        | of the License, or (at your option) any later version.                      |
        |                                                                             |
        | This program is distributed in the hope that it will be useful,             |
        | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
        | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
        | GNU General Public License for more details.                                |
        |                                                                             |
        | You should have received a copy of the GNU General Public License           |
        | along with this program; if not, write to the Free Software                 |
        | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
        +-----------------------------------------------------------------------------+
*/

/**
 * Base class for course and group waiting lists
 * @author  Stefan Meyer <smeyer.ilias@gmx.de>
 * @ingroup ServicesMembership
 */
abstract class ilWaitingList
{
    private int $obj_id = 0;
    private array $user_ids = [];
    private $users = [];

    protected ilDBInterface $db;
    protected ilAppEventHandler $eventHandler;


    public static array $is_on_list = [];

    public function __construct(int $a_obj_id)
    {
        global $DIC;

        $this->db = $DIC->database();
        $this->eventHandler = $DIC->event();
        $this->obj_id = $a_obj_id;
        if ($a_obj_id) {
            $this->read();
        }
    }

    public static function lookupListSize(int $a_obj_id) : int
    {
        global $DIC;

        $ilDB = $DIC->database();
        $query = 'SELECT count(usr_id) num from crs_waiting_list WHERE obj_id = ' . $ilDB->quote($a_obj_id, 'integer');
        $res = $ilDB->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return (int) $row->num;
        }
        return 0;
    }

    public static function _deleteAll(int $a_obj_id) : void
    {
        global $DIC;

        $ilDB = $DIC->database();

        $query = "DELETE FROM crs_waiting_list WHERE obj_id = " . $ilDB->quote($a_obj_id, 'integer') . " ";
        $res = $ilDB->manipulate($query);
    }

    public static function _deleteUser(int $a_usr_id) : void
    {
        global $DIC;

        $ilDB = $DIC->database();
        $query = "DELETE FROM crs_waiting_list WHERE usr_id = " . $ilDB->quote($a_usr_id, 'integer');
        $res = $ilDB->manipulate($query);
    }

    public static function deleteUserEntry(int $a_usr_id, int $a_obj_id) : void
    {
        global $DIC;

        $ilDB = $DIC->database();
        $query = "DELETE FROM crs_waiting_list " .
            "WHERE usr_id = " . $ilDB->quote($a_usr_id, 'integer') . ' ' .
            "AND obj_id = " . $ilDB->quote($a_obj_id, 'integer');
        $ilDB->query($query);
    }

    public function getObjId() : int
    {
        return $this->obj_id;
    }

    public function addToList(int $a_usr_id) : bool
    {
        if ($this->isOnList($a_usr_id)) {
            return false;
        }
        $query = "INSERT INTO crs_waiting_list (obj_id,usr_id,sub_time) " .
            "VALUES (" .
            $this->db->quote($this->getObjId(), 'integer') . ", " .
            $this->db->quote($a_usr_id, 'integer') . ", " .
            $this->db->quote(time(), 'integer') . " " .
            ")";
        $res = $this->db->manipulate($query);
        $this->read();
        return true;
    }

    public function updateSubscriptionTime(int $a_usr_id, int $a_subtime) : void
    {
        $query = "UPDATE crs_waiting_list " .
            "SET sub_time = " . $this->db->quote($a_subtime, 'integer') . " " .
            "WHERE usr_id = " . $this->db->quote($a_usr_id, 'integer') . " " .
            "AND obj_id = " . $this->db->quote($this->getObjId(), 'integer') . " ";
        $res = $this->db->manipulate($query);
    }

    public function removeFromList(int $a_usr_id) : bool
    {
        $query = "DELETE FROM crs_waiting_list " .
            " WHERE obj_id = " . $this->db->quote($this->getObjId(), 'integer') . " " .
            " AND usr_id = " . $this->db->quote($a_usr_id, 'integer') . " ";
        $affected = $this->db->manipulate($query);
        $this->read();
        return $affected > 0;
    }

    public function isOnList(int $a_usr_id) : bool
    {
        return isset($this->users[$a_usr_id]);
    }

    public static function _isOnList(int $a_usr_id, int $a_obj_id) : bool
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];
        if (isset(self::$is_on_list[$a_usr_id][$a_obj_id])) {
            return self::$is_on_list[$a_usr_id][$a_obj_id];
        }

        $query = "SELECT usr_id " .
            "FROM crs_waiting_list " .
            "WHERE obj_id = " . $ilDB->quote($a_obj_id, 'integer') . " " .
            "AND usr_id = " . $ilDB->quote($a_usr_id, 'integer');
        $res = $ilDB->query($query);
        return (bool) $res->numRows();
    }

    /**
     * Preload on list info. This is used, e.g. in the repository
     * to prevent multiple reads on the waiting list table.
     * The function is triggered in the preload functions of ilObjCourseAccess
     * and ilObjGroupAccess.
     */
    public static function _preloadOnListInfo(array $a_usr_ids, array $a_obj_ids) : void
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];
        foreach ($a_usr_ids as $usr_id) {
            foreach ($a_obj_ids as $obj_id) {
                self::$is_on_list[$usr_id][$obj_id] = false;
            }
        }
        $query = "SELECT usr_id, obj_id " .
            "FROM crs_waiting_list " .
            "WHERE " .
            $ilDB->in("obj_id", $a_obj_ids, false, "integer") . " AND " .
            $ilDB->in("usr_id", $a_usr_ids, false, "integer");
        $res = $ilDB->query($query);
        while ($rec = $ilDB->fetchAssoc($res)) {
            self::$is_on_list[$rec["usr_id"]][$rec["obj_id"]] = true;
        }
    }

    public function getCountUsers() : int
    {
        return count($this->users);
    }

    public function getPosition(int $a_usr_id) : int
    {
        return isset($this->users[$a_usr_id]) ? $this->users[$a_usr_id]['position'] : -1;
    }

    /**
     * get all users on waiting list
     * @access public
     * @return array<int, array<{position: int, time: int, usr_id: int}>>
     */
    public function getAllUsers() : array
    {
        return $this->users;
    }

    /**
     * get user
     * @param int usr_id
     * @return array<{position: int, time: int, usr_id: int}>
     */
    public function getUser(int $a_usr_id) : array
    {
        return $this->users[$a_usr_id] ?? [];
    }

    /**
     * @return int[]
     */
    public function getUserIds()
    {
        return $this->user_ids;
    }

    private function read() : void
    {
        $this->users = [];
        $query = "SELECT * FROM crs_waiting_list " .
            "WHERE obj_id = " . $this->db->quote($this->getObjId(), 'integer') . " ORDER BY sub_time";

        $res = $this->db->query($query);
        $counter = 0;
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            ++$counter;
            $this->users[$row->usr_id]['position'] = $counter;
            $this->users[$row->usr_id]['time'] = $row->sub_time;
            $this->users[$row->usr_id]['usr_id'] = $row->usr_id;

            $this->user_ids[] = $row->usr_id;
        }
    }
}
