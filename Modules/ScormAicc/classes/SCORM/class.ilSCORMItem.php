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
* SCORM Item
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
*
* @ingroup ModulesScormAicc
*/
class ilSCORMItem extends ilSCORMObject
{
    public string $import_id;
    public string $identifierref;
    public bool $isvisible;
    public ?string $parameters = null;
    public ?string $prereq_type = null;
    public ?string $prerequisites = null;
    public ?string $maxtimeallowed = null;
    public ?string $timelimitaction = null;
    public ?string $datafromlms = null;
    public ?string $masteryscore = null;

    /**
    * Constructor
    *
    * @param	int		$a_id		Object ID
    * @access	public
    */
    public function __construct(int $a_id = 0)
    {
        parent::__construct($a_id);
        $this->setType("sit");
    }

    /**
     * @return string
     */
    public function getImportId() : string
    {
        return $this->import_id;
    }

    /**
     * @param string $a_import_id
     * @return void
     */
    public function setImportId(string $a_import_id) : void
    {
        $this->import_id = $a_import_id;
    }

    /**
     * @return string
     */
    public function getIdentifierRef() : string
    {
        return $this->identifierref;
    }

    /**
     * @param string $a_id_ref
     * @return void
     */
    public function setIdentifierRef(string $a_id_ref) : void
    {
        $this->identifierref = $a_id_ref;
    }

    /**
     * @return bool
     */
    public function getVisible() : bool
    {
        return $this->isvisible;
    }

    /**
     * @param bool $a_visible
     * @return void
     */
    public function setVisible(bool $a_visible) : void
    {
        $this->isvisible = $a_visible;
    }

    /**
     * @return string|null
     */
    public function getParameters() : ?string
    {
        return $this->parameters;
    }

    /**
     * @param string|null $a_par
     * @return void
     */
    public function setParameters(?string $a_par) : void
    {
        $this->parameters = $a_par;
    }

    /**
     * @return string|null
     */
    public function getPrereqType() : ?string
    {
        return $this->prereq_type;
    }

    /**
     * @param string|null $a_p_type
     * @return void
     */
    public function setPrereqType(?string $a_p_type) : void
    {
        $this->prereq_type = $a_p_type;
    }

    /**
     * @return string|null
     */
    public function getPrerequisites() : ?string
    {
        return $this->prerequisites;
    }

    /**
     * @param string|null $a_pre
     * @return void
     */
    public function setPrerequisites(?string $a_pre) : void
    {
        $this->prerequisites = $a_pre;
    }

    /**
     * @return string|null
     */
    public function getMaxTimeAllowed() : ?string
    {
        return $this->maxtimeallowed;
    }

    /**
     * @param string|null $a_max
     * @return void
     */
    public function setMaxTimeAllowed(?string $a_max) : void
    {
        $this->maxtimeallowed = $a_max;
    }

    /**
     * @return string|null
     */
    public function getTimeLimitAction() : ?string
    {
        return $this->timelimitaction;
    }

    /**
     * @param string|null $a_lim_act
     * @return void
     */
    public function setTimeLimitAction(?string $a_lim_act) : void
    {
        $this->timelimitaction = $a_lim_act;
    }

    /**
     * @return string|null
     */
    public function getDataFromLms() : ?string
    {
        return $this->datafromlms;
    }

    /**
     * @param string|null $a_data
     * @return void
     */
    public function setDataFromLms(?string $a_data) : void
    {
        $this->datafromlms = $a_data;
    }

    /**
     * @return string|null
     */
    public function getMasteryScore() : ?string
    {
        return $this->masteryscore;
    }

    /**
     * @param string|null $a_score
     * @return void
     */
    public function setMasteryScore(?string $a_score) : void
    {
        $this->masteryscore = $a_score;
    }

    /**
     * @return void
     */
    public function read() : void
    {
        global $DIC;
        $ilDB = $DIC->database();
        
        parent::read();

        $obj_set = $ilDB->queryF(
            'SELECT * FROM sc_item WHERE obj_id = %s',
            array('integer'),
            array($this->getId())
        );
        $obj_rec = $ilDB->fetchAssoc($obj_set);
        
        $this->setImportId($obj_rec["import_id"]);
        $this->setIdentifierRef($obj_rec["identifierref"]);
        if (strtolower($obj_rec["isvisible"]) == "false") {
            $this->setVisible(false);
        } else {
            $this->setVisible(true);
        }
        $this->setParameters($obj_rec["parameters"]);
        $this->setPrereqType($obj_rec["prereq_type"]);
        $this->setPrerequisites($obj_rec["prerequisites"]);
        $this->setMaxTimeAllowed($obj_rec["maxtimeallowed"]);
        $this->setTimeLimitAction($obj_rec["timelimitaction"]);
        $this->setDataFromLms($obj_rec["datafromlms"]);
        $this->setMasteryScore($obj_rec["masteryscore"]);
    }

    /**
     * @return void
     */
    public function create() : void
    {
        global $DIC;
        $ilDB = $DIC->database();
        
        parent::create();

        $str_visible = ($this->getVisible()) ? 'true' : 'false';
        
        $ilDB->insert('sc_item', array(
            'obj_id' => array('integer', $this->getId()),
            'import_id' => array('text', $this->getImportId()),
            'identifierref' => array('text', $this->getIdentifierRef()),
            'isvisible' => array('text', $str_visible),
            'parameters' => array('text', $this->getParameters()),
            'prereq_type' => array('text', $this->getPrereqType()),
            'prerequisites' => array('text', $this->getPrerequisites()),
            'maxtimeallowed' => array('text', $this->getMaxTimeAllowed()),
            'timelimitaction' => array('text', $this->getTimeLimitAction()),
            'datafromlms' => array('clob', $this->getDataFromLms()),
            'masteryscore' => array('text', $this->getMasteryScore())
        ));
    }

    /**
     * @return void
     */
    public function update() : void
    {
        global $DIC;
        $ilDB = $DIC->database();

        parent::update();
        
        $str_visible = ($this->getVisible()) ? 'true' : 'false';
        
        $ilDB->update(
            'sc_item',
            array(
                'import_id' => array('text', $this->getImportId()),
                'identifierref' => array('text', $this->getIdentifierRef()),
                'isvisible' => array('text', $str_visible),
                'parameters' => array('text', $this->getParameters()),
                'prereq_type' => array('text', $this->getPrereqType()),
                'prerequisites' => array('text', $this->getPrerequisites()),
                'maxtimeallowed' => array('text', $this->getMaxTimeAllowed()),
                'timelimitaction' => array('text', $this->getTimeLimitAction()),
                'datafromlms' => array('clob', $this->getDataFromLms()),
                'masteryscore' => array('text', $this->getMasteryScore())
            ),
            array(
                'obj_id' => array('integer', $this->getId())
            )
        );
    }

    /**
     * get tracking data of specified or current user
     *
     * @return array<int|string, mixed>
     */
    public function getTrackingDataOfUser(int $a_user_id = 0) : array
    {
        global $DIC;
        $ilDB = $DIC->database();
        $ilUser = $DIC->user();

        if ($a_user_id == 0) {
            $a_user_id = $ilUser->getId();
        }
        
        $track_set = $ilDB->queryF(
            '
			SELECT lvalue, rvalue FROM scorm_tracking 
			WHERE sco_id = %s 
			AND user_id =  %s
			AND obj_id = %s',
            array('integer', 'integer', 'integer'),
            array($this->getId(), $a_user_id, $this->getSLMId())
        );
        
        $trdata = array();
        while ($track_rec = $ilDB->fetchAssoc($track_set)) {
            $trdata[$track_rec["lvalue"]] = $track_rec["rvalue"];
        }

        return $trdata;
    }

    /**
     * @param int $a_item_id
     * @param int $a_user_id
     * @param int $a_obj_id
     * @return array<int|string, mixed>
     */
    public static function _lookupTrackingDataOfUser(int $a_item_id, int $a_user_id = 0, int $a_obj_id = 0) : array
    {
        global $DIC;
        $ilDB = $DIC->database();
        $ilUser = $DIC->user();

        if ($a_user_id == 0) {
            $a_user_id = $ilUser->getId();
        }

        $track_set = $ilDB->queryF(
            '
			SELECT lvalue, rvalue FROM scorm_tracking 
			WHERE sco_id = %s 
			AND user_id =  %s
			AND obj_id = %s',
            array('integer', 'integer', 'integer'),
            array($a_item_id, $a_user_id, $a_obj_id)
        );
        
        $trdata = array();
        while ($track_rec = $ilDB->fetchAssoc($track_set)) {
            $trdata[$track_rec["lvalue"]] = $track_rec["rvalue"];
        }

        return $trdata;
    }

    /**
     * @return void
     */
    public function delete() : void
    {
        global $DIC;
        $ilDB = $DIC->database();
        $ilLog = ilLoggerFactory::getLogger('sahs');

        parent::delete();

        $ilDB->manipulateF(
            'DELETE FROM sc_item WHERE obj_id = %s',
            array('integer'),
            array($this->getId())
        );
        
        $ilLog->write("SAHS Delete(ScormItem): " .
            'DELETE FROM scorm_tracking WHERE sco_id = ' . $this->getId() . ' AND obj_id = ' . $this->getSLMId());
        $ilDB->manipulateF(
            'DELETE FROM scorm_tracking WHERE sco_id = %s AND obj_id = %s',
            array('integer', 'integer'),
            array($this->getId(), $this->getSLMId())
        );
        ilLPStatusWrapper::_refreshStatus($this->getSLMId());
    }

    /**
     * @param string $a_lval
     * @param string $a_rval
     * @param int    $a_obj_id
     * @return void
     */
    public function insertTrackData(string $a_lval, string $a_rval, int $a_obj_id) : void
    {
        //ilObjSCORMTracking::_insertTrackData($this->getId(), $a_lval, $a_rval, $a_ref_id);
        ilObjSCORMTracking::_insertTrackData($this->getId(), $a_lval, $a_rval, $a_obj_id);
    }

    /**
     * @param int $a_obj_id
     * @return array
     */
    public static function _getItems(int $a_obj_id)
    {
        global $DIC;
        $ilDB = $DIC->database();
        $item_ids = [];

        $res = $ilDB->queryF(
            '
			SELECT obj_id FROM scorm_object 
			WHERE slm_id = %s
			AND c_type = %s',
            array('integer', 'text'),
            array($a_obj_id, 'sit')
        );
        while ($row = $ilDB->fetchObject($res)) {
            $item_ids[] = $row->obj_id;
        }
        return $item_ids;
    }

    /**
     * @param int $a_obj_id
     * @return string
     */
    public static function _lookupTitle(int $a_obj_id) : string
    {
        global $DIC;
        $ilDB = $DIC->database();

        $res = $ilDB->queryF(
            'SELECT title FROM scorm_object WHERE obj_id = %s',
            array('integer'),
            array($a_obj_id)
        );
        
        while ($row = $ilDB->fetchObject($res)) {
            return $row->title;
        }
        return '';
    }
}
