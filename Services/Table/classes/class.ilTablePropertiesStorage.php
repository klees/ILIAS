<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

/**
 * Saves (mostly asynchronously) user properties of tables (e.g. filter on/off)
 * @author Alexander Killing <killing@leifos.de>
 * @ilCtrl_Calls ilTablePropertiesStorage: ilTablePropertiesStorage
 */
class ilTablePropertiesStorage implements ilCtrlBaseClassInterface
{
    protected ?\ILIAS\Table\TableGUIRequest $table_request = null;
    protected ilObjUser $user;
    protected ilCtrl $ctrl;
    protected ilDBInterface $db;
    public array $properties = array(
        "filter" => array("storage" => "db"),
        "direction" => array("storage" => "db"),
        "order" => array("storage" => "db"),
        "rows" => array("storage" => "db"),
        "offset" => array("storage" => "session"),
        "selfields" => array("storage" => "db"),
        "selfilters" => array("storage" => "db"),
        "filter_values" => array("storage" => "db")
    );

    public function __construct()
    {
        global $DIC;

        $this->user = $DIC->user();
        $this->ctrl = $DIC->ctrl();
        $this->db = $DIC->database();
        if (isset($DIC["http"])) {
            $this->table_request = new \ILIAS\Table\TableGUIRequest(
                $DIC->http(),
                $DIC->refinery()
            );
        }
    }


    public function executeCommand() : void
    {
        $ilCtrl = $this->ctrl;
        $cmd = $ilCtrl->getCmd();
        $this->$cmd();
    }
    
    public function showFilter() : void
    {
        $ilUser = $this->user;

        $requested_user_id = $this->table_request->getUserId();
        $requested_table_id = $this->table_request->getTableId();

        if ($requested_user_id == $ilUser->getId()) {
            $this->storeProperty(
                $requested_table_id,
                $requested_user_id,
                "filter",
                1
            );
        }
    }
    
    public function hideFilter() : void
    {
        $ilUser = $this->user;

        $requested_user_id = $this->table_request->getUserId();
        $requested_table_id = $this->table_request->getTableId();

        if ($requested_user_id == $ilUser->getId()) {
            $this->storeProperty(
                $requested_table_id,
                $requested_user_id,
                "filter",
                0
            );
        }
    }
    
    public function storeProperty(
        string $a_table_id,
        int $a_user_id,
        string $a_property,
        string $a_value
    ) : void {
        $ilDB = $this->db;

        if ($a_table_id == "" || !$this->isValidProperty($a_property)) {
            return;
        }
        
        $storage = $this->properties[$a_property]["storage"];
        if ($a_user_id == ANONYMOUS_USER_ID) {
            $storage = "session";
        }
        
        switch ($storage) {
            case "session":
                ilSession::set("table_" . $a_table_id . "_" . $a_user_id . "_" . $a_property, $a_value);
                break;
                
            case "db":
                $ilDB->replace(
                    "table_properties",
                    array(
                    "table_id" => array("text", $a_table_id),
                    "user_id" => array("integer", $a_user_id),
                    "property" => array("text", $a_property)),
                    array(
                    "value" => array("text", $a_value)
                    )
                );
        }
    }
    
    public function getProperty(
        string $a_table_id,
        int $a_user_id,
        string $a_property
    ) : string {
        $ilDB = $this->db;

        if ($a_table_id == "" || !$this->isValidProperty($a_property)) {
            return "";
        }

        $storage = $this->properties[$a_property]["storage"];
        if ($a_user_id == ANONYMOUS_USER_ID) {
            $storage = "session";
        }
        
        switch ($storage) {
            case "session":
                return ilSession::get("table_" . $a_table_id . "_" . $a_user_id . "_" . $a_property) ?? "";

            case "db":
                $set = $ilDB->query(
                    $q = "SELECT value FROM table_properties " .
                    " WHERE table_id = " . $ilDB->quote($a_table_id, "text") .
                    " AND user_id = " . $ilDB->quote($a_user_id, "integer") .
                    " AND property = " . $ilDB->quote($a_property, "text")
                );
                $rec = $ilDB->fetchAssoc($set);
                return $rec["value"] ?? '';
        }
        return "";
    }

    /**
     * Check if given property id is valid
     */
    public function isValidProperty($a_property) : bool
    {
        if (array_key_exists($a_property, $this->properties)) {
            return true;
        }
        return false;
    }
}
