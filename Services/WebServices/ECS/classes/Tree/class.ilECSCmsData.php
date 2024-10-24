<?php declare(strict_types=1);

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/

/**
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilECSCmsData
{
    const MAPPING_UNMAPPED = 1;
    const MAPPING_PENDING_DISCONNECTABLE = 2;
    const MAPPING_PENDING_NOT_DISCONNECTABLE = 3;
    const MAPPING_MAPPED = 4;
    const MAPPING_DELETED = 5;

    private ilDBInterface $db;
    
    private int $obj_id;
    private int $server_id;
    private int $mid;
    private int $tree_id;
    private string $cms_id;
    private string $title;
    private string $term;
    private int $status = self::MAPPING_UNMAPPED;
    private bool $deleted = false;

    public function __construct(int $a_obj_id = 0)
    {
        global $DIC;
        $this->db = $DIC->database();
        
        $this->obj_id = $a_obj_id;
        $this->read();
    }

    public static function treeExists(int $a_server_id, int $a_mid, int $a_tree_id) : bool
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'SELECT COUNT(*) num FROM ecs_cms_data ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id  = ' . $ilDB->quote($a_tree_id, 'integer');

        $res = $ilDB->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->num > 0 ? true : false;
        }
        return false;
    }
    
    /**
     * Find deleted nodes
     * Uses a left join since this is more robust. An alternative implementation
     * could simply check the deleted flag in ecs_cms_data.
     */
    public static function findDeletedNodes(int $a_server_id, int $a_mid, int $a_tree_id) : array
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];
        
        $query = 'SELECT ed.obj_id obj_id FROM ecs_cms_data ed ' .
                'LEFT JOIN ecs_cms_tree et ON ed.obj_id = et.child ' .
                'WHERE et.child IS NULL ' .
                'AND server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
                'AND mid = ' . $ilDB->quote($a_mid) . ' ' .
                'AND tree_id  = ' . $ilDB->quote($a_tree_id);
        $res = $ilDB->query($query);
        
        $deleted = array();
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $deleted[] = $row->obj_id;
        }
        return $deleted;
    }

    public static function lookupObjId(int $a_server_id, int $a_mid, int $a_tree_id, string $cms_id)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];
        $logger = $DIC->logger()->wsrv();
        
        $query = 'SELECT obj_id FROM ecs_cms_data ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer') . ' ' .
            'AND cms_id = ' . $ilDB->quote($cms_id, 'text');
        $res = $ilDB->query($query);
        
        $logger->debug($query);
        
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->obj_id;
        }
        return 0;
    }

    /**
     * Lookup first obj_id of cms node
     */
    public static function lookupFirstTreeOfNode($a_server_id, $a_mid, $cms_id)
    {
        global $DIC;

        $ilDB = $DIC->database();
        $logger = $DIC->logger()->wsrv();
        
        $logger->debug($a_server_id . ' ' . $a_mid . ' ' . $cms_id);

        $query = 'SELECT tree_id FROM ecs_cms_data ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND cms_id = ' . $ilDB->quote($cms_id, 'text') . ' ' .
            'ORDER BY tree_id ';
        $res = $ilDB->query($query);
        
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->tree_id;
        }
        return 0;
    }

    /**
     * Lookup title by obj id
     * @param int $a_obj_id
     */
    public static function lookupTitle($a_server_id, $a_mid, $a_tree_id)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'SELECT * FROM ecs_cms_data ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer');
        $res = $ilDB->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->title;
        }
        return '';
    }

    /**
     * Lookup term (highest term in cms tree)
     * @param <type> $a_server_id
     * @param <type> $a_mid
     * @param <type> $a_tree_id
     */
    public static function lookupTopTerm($a_server_id, $a_mid, $a_tree_id)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'SELECT term FROM ecs_cms_data ' .
            'JOIN ecs_cms_tree ON obj_id = child ' .
            'WHERE tree = ' . $ilDB->quote($a_tree_id, 'integer') . ' ' .
            'AND server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer') . ' ' .
            'ORDER BY depth';
        $res = $ilDB->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->term;
        }
        return '';
    }

    /**
     * Lookup status
     * @param int $a_obj_id
     */
    public static function lookupStatusByObjId($a_server_id, $a_mid, $a_tree_id, $obj_id)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'SELECT status,deleted FROM ecs_cms_data ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer') . ' ' .
            'AND obj_id = ' . $ilDB->quote($obj_id, 'integer');
        $res = $ilDB->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            if ($row->deleted) {
                return self::MAPPING_DELETED;
            }
            return $row->status;
        }
        return self::MAPPING_UNMAPPED;
    }

    /**
     * Lookup status
     * @param int $a_obj_id
     */
    public static function lookupStatusByCmsId($a_server_id, $a_mid, $a_tree_id, $cms_id)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'SELECT status FROM ecs_cms_data ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer') . ' ' .
            'AND cms_id = ' . $ilDB->quote($cms_id, 'text');
        $res = $ilDB->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            return $row->status;
        }
        return self::MAPPING_UNMAPPED;
    }

    public static function updateStatus($a_server_id, $a_mid, $a_tree_id)
    {
        // Set all status to pending unmapped
        self::writeAllStatus($a_server_id, $a_mid, $a_tree_id, self::MAPPING_UNMAPPED);

        // Set mapped for mapped and their descendent
        foreach (ilECSNodeMappingAssignments::lookupAssignmentIds($a_server_id, $a_mid, $a_tree_id) as $assignment) {
            $cmsTree = new ilECSCmsTree($a_tree_id);
            $subIds = self::lookupCmsIds(array_merge($cmsTree->getSubTreeIds($assignment), array($assignment)));

            self::writeStatus(
                $a_server_id,
                $a_mid,
                $a_tree_id,
                $subIds,
                self::MAPPING_MAPPED
            );
        }
    }

    /**
     * Lookup cms id
     * @param type $a_obj_id
     */
    public static function lookupCmsId($a_obj_id)
    {
        $cms_ids = self::lookupCmsIds(array($a_obj_id));
        return $cms_ids[0];
    }


    public static function lookupCmsIds($a_obj_ids)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'SELECT cms_id FROM ecs_cms_data ' .
            'WHERE ' . $ilDB->in('obj_id', $a_obj_ids, false, 'integer');
        $res = $ilDB->query($query);

        $cms_ids = array();
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $cms_ids[] = $row->cms_id;
        }
        return $cms_ids;
    }
    
    /**
     * @param type $a_server_id
     * @param type $a_mid
     * @param type $a_tree_id
     */
    public static function lookupCmsIdsOfTree($a_server_id, $a_mid, $a_tree_id)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];
        
        $query = 'SELECT cms_id FROM ecs_cms_data ' .
                'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
                'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
                'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer');
        $res = $ilDB->query($query);
        $cms_ids = array();
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $cms_ids[] = $row->cms_id;
        }
        return $cms_ids;
    }

    /**
     * Update status
     * @param <type> $a_server_id
     * @param <type> $a_mid
     * @param <type> $a_tree_id
     * @param <type> $cms_ids
     * @param <type> $status
     * @return <type>
     */
    public static function writeStatus($a_server_id, $a_mid, $a_tree_id, $cms_ids, $status)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'UPDATE ecs_cms_data ' .
            'SET status = ' . $ilDB->quote($status, 'integer') . ' ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer') . ' ' .
            'AND ' . $ilDB->in('cms_id', $cms_ids, false, 'text');
        $ilDB->manipulate($query);
        return true;
    }

    /**
     * Update status
     * @param <type> $a_server_id
     * @param <type> $a_mid
     * @param <type> $a_tree_id
     * @param <type> $cms_ids
     * @param <type> $status
     * @return <type>
     */
    public static function writeAllStatus($a_server_id, $a_mid, $a_tree_id, $status)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'UPDATE ecs_cms_data ' .
            'SET status = ' . $ilDB->quote($status, 'integer') . ' ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer') . ' ';
        $ilDB->manipulate($query);
        return true;
    }
    
    /**
     * Write deleted status
     * @param type $a_server_id
     * @param type $a_mid
     * @param type $a_tree_id
     * @param type $a_deleted_flag
     */
    public static function writeAllDeleted($a_server_id, $a_mid, $a_tree_id, $a_deleted_flag)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'UPDATE ecs_cms_data ' .
            'SET deleted = ' . $ilDB->quote($a_deleted_flag, 'integer') . ' ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer') . ' ' .
            'AND tree_id = ' . $ilDB->quote($a_tree_id, 'integer') . ' ';
        $ilDB->manipulate($query);
        return true;
    }

    public static function lookupTreeIds($a_server_id, $a_mid)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = 'SELECT DISTINCT(tree_id) tid FROM ecs_cms_data ' .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer') . ' ' .
            'AND mid = ' . $ilDB->quote($a_mid, 'integer');
        $res = $ilDB->query($query);

        $tree_ids = array();
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $tree_ids[] = $row->tid;
        }
        return (array) $tree_ids;
    }


    public function setTitle($a_title)
    {
        $this->title = $a_title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTerm($a_term)
    {
        $this->term = $a_term;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function setObjId($a_id)
    {
        $this->obj_id = $a_id;
    }

    public function getObjId()
    {
        return $this->obj_id;
    }

    public function setCmsId($a_id)
    {
        $this->cms_id = $a_id;
    }

    public function getCmsId()
    {
        return $this->cms_id;
    }

    public function setServerId($a_id)
    {
        $this->server_id = $a_id;
    }

    public function getServerId()
    {
        return $this->server_id;
    }

    public function setTreeId($a_id)
    {
        $this->tree_id = $a_id;
    }

    public function getTreeId()
    {
        return $this->tree_id;
    }

    public function setMid($a_id)
    {
        $this->mid = $a_id;
    }

    public function getMid()
    {
        return $this->mid;
    }

    public function setStatus($a_status)
    {
        $this->status = $a_status;
    }

    public function getStatus()
    {
        return $this->status;
    }
    
    public function setDeleted($a_is_deleted)
    {
        $this->deleted = $a_is_deleted;
    }
    
    public function isDeleted()
    {
        return $this->deleted;
    }
    

    public function save()
    {
        $this->obj_id = $this->db->nextId('ecs_cms_data');

        $query = 'INSERT INTO ecs_cms_data (obj_id,server_id,mid,tree_id,cms_id,title,term,status,deleted) ' .
            'VALUES ( ' .
            $this->db->quote($this->obj_id, 'integer') . ', ' .
            $this->db->quote($this->server_id, 'integer') . ', ' .
            $this->db->quote($this->mid, 'integer') . ', ' .
            $this->db->quote($this->tree_id, 'integer') . ', ' .
            $this->db->quote($this->cms_id, 'text') . ', ' .
            $this->db->quote($this->title, 'text') . ', ' .
            $this->db->quote($this->term, 'text') . ', ' .
            $this->db->quote($this->status, 'integer') . ', ' .
            $this->db->quote($this->deleted, 'integer') . ' ' .
            ')';
        $this->db->manipulate($query);
        return true;
    }

    public function update()
    {
        $query = "UPDATE ecs_cms_data SET " .
            'title = ' . $this->db->quote($this->title, 'text') . ', ' .
            'term = ' . $this->db->quote($this->term, 'text') . ', ' .
            'status = ' . $this->db->quote($this->status, 'text') . ', ' .
            'deleted = ' . $this->db->quote($this->isDeleted(), 'integer') . ' ' .
            'WHERE obj_id = ' . $this->db->quote($this->obj_id, 'integer');
        $this->db->manipulate($query);
    }

    public function delete()
    {
        $query = "DELETE FROM ecs_cms_data  " .
            'WHERE obj_id = ' . $this->db->quote($this->obj_id, 'integer');
        $this->db->manipulate($query);
    }

    public function deleteTree()
    {
        $query = "DELETE FROM ecs_cms_data  " .
            'WHERE server_id = ' . $this->db->quote($this->server_id, 'integer') . ' ' .
            'AND mid = ' . $this->db->quote($this->mid, 'integer') . ' ' .
            'AND tree_id = ' . $this->db->quote($this->tree_id, 'integer') . ' ';
        $this->db->manipulate($query);
    }
    
    public static function deleteByServerId($a_server_id)
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = "DELETE FROM ecs_cms_data  " .
            'WHERE server_id = ' . $ilDB->quote($a_server_id, 'integer');
        $ilDB->manipulate($query);
    }

    protected function read()
    {
        $query = 'SELECT * FROM ecs_cms_data ' .
            'WHERE obj_id = ' . $this->db->quote($this->obj_id, 'integer');
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $this->title = $row->title;
            $this->term = $row->term;
            $this->server_id = $row->server_id;
            $this->mid = $row->mid;
            $this->tree_id = $row->tree_id;
            $this->cms_id = $row->cms_id;
            $this->status = $row->status;
            $this->deleted = $row->deleted;
        }
    }
}
