<?php declare(strict_types=1);

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * OER harvester object status
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 *
 */
class ilOerHarvesterObjectStatus
{
    private int $obj_id;

    private int $harvest_ref_id;

    private bool $blocked = false;

    protected ilDBInterface $db;



    public function __construct(int $obj_id = 0)
    {
        global $DIC;

        $this->db = $DIC->database();

        $this->obj_id = $obj_id;
        if ($this->obj_id) {
            $this->read();
        }
    }

    /**
     * @return int[]
     */
    public static function lookupHarvested() : array
    {
        global $DIC;

        $db = $DIC->database();

        $query = 'SELECT href_id FROM il_meta_oer_stat ';
        $res = $db->query($query);

        $hids = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $hids[] = $row->href_id;
        }
        return $hids;
    }


    public static function lookupObjIdByHarvestingId(int $a_href_id) : int
    {
        global $DIC;

        $db = $DIC->database();
        $query = 'SELECT obj_id FROM il_meta_oer_stat ' .
            'WHERE href_id = ' . $db->quote($a_href_id, 'integer');
        $res = $db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->obj_id;
        }
        return 0;
    }

    public function setObjId(int $a_obj_id) : void
    {
        $this->obj_id = $a_obj_id;
    }

    public function getObjId() : int
    {
        return $this->obj_id;
    }

    public function setHarvestRefId(int $a_ref_id) : void
    {
        $this->harvest_ref_id = $a_ref_id;
    }

    public function getHarvestRefId() : int
    {
        return $this->harvest_ref_id;
    }

    public function setBlocked(bool $a_stat) : void
    {
        $this->blocked = $a_stat;
    }

    public function isBlocked() : bool
    {
        return $this->blocked;
    }

    public function isCreated() : bool
    {
        return (bool) $this->harvest_ref_id;
    }


    public function save() : bool
    {
        $this->delete();
        $query = 'INSERT INTO il_meta_oer_stat ' .
            '(obj_id, href_id, blocked ) ' .
            'VALUES (' .
            $this->db->quote($this->getObjId(), 'integer') . ', ' .
            $this->db->quote($this->getHarvestRefId(), 'integer') . ', ' .
            $this->db->quote($this->isBlocked(), 'integer') .
            ')';
        $res = $this->db->manipulate($query);
        return true;
    }


    public function delete() : bool
    {
        $query = 'DELETE FROM il_meta_oer_stat ' .
            'WHERE obj_id = ' . $this->db->quote($this->getObjId(), 'integer');
        $this->db->manipulate($query);
        return true;
    }



    public function read() : void
    {
        $query = 'SELECT * FROM il_meta_oer_stat ' .
            'WHERE obj_id = ' . $this->db->quote($this->getObjId(), 'integer');
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $this->setObjId($row->obj_id);
            $this->setHarvestRefId($row->href_id);
            $this->setBlocked((bool) $row->blocked);
        }
    }
}
