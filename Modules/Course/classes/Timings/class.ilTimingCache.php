<?php declare(strict_types=0);
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
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
 * class ilTimingCache
 * @author Stefan Meyer <meyer@leifos.com>
 */
class ilTimingCache
{
    private static array $instances = [];

    private int $ref_id = 0;
    private int $obj_id = 0;
    private bool $timings_active = false;
    private array $timings = array();
    private array $timings_user = array();
    private array $collection_items = array();
    private array $completed_users = array();

    public function __construct(int $ref_id)
    {
        $this->ref_id = $ref_id;
        $this->obj_id = ilObject::_lookupObjId($this->ref_id);
        $this->readObjectInformation();
    }

    public static function getInstanceByRefId(int $ref_id) : ilTimingCache
    {
        if (!isset(self::$instances[$ref_id])) {
            self::$instances[$ref_id] = new self($ref_id);
        }
        return self::$instances[$ref_id];
    }

    public function isWarningRequired(int $usr_id) : bool
    {
        if (in_array($usr_id, $this->completed_users)) {
            return false;
        }
        foreach ($this->collection_items as $item) {
            $item_instance = self::getInstanceByRefId($item);
            if ($item_instance->isWarningRequired($usr_id)) {
                return true;
            }
        }
        if (!$this->timings_active) {
            return false;
        }

        // check constraints
        if ($this->timings['changeable'] && isset($this->timings_user[$usr_id]['end'])) {
            $end = $this->timings_user[$usr_id]['end'];
        } else {
            $end = $this->timings['suggestion_end'];
        }
        return $end < time();
    }

    protected function readObjectInformation() : void
    {
        $this->timings = ilObjectActivation::getItem($this->ref_id);
        $this->timings_active = false;
        if ($this->timings['timing_type'] == ilObjectActivation::TIMINGS_PRESETTING) {
            $this->timings_active = true;
            $this->timings_user = ilTimingPlaned::_getPlanedTimingsByItem($this->ref_id);
        }

        $olp = ilObjectLP::getInstance($this->obj_id);
        $collection = $olp->getCollectionInstance();
        if ($collection instanceof ilLPCollectionOfRepositoryObjects) {
            $this->collection_items = $collection->getItems();
        }
        $this->completed_users = ilLPStatus::_getCompleted($this->obj_id);
    }

    public static function _getTimings(int $a_ref_id) : array
    {
        static $cache = array();

        if (isset($cache[$a_ref_id])) {
            return $cache[$a_ref_id];
        }
        $cache[$a_ref_id]['item'] = ilObjectActivation::getItem($a_ref_id);
        $cache[$a_ref_id]['user'] = ilTimingPlaned::_getPlanedTimingsByItem($a_ref_id);

        return $cache[$a_ref_id];
    }
}
