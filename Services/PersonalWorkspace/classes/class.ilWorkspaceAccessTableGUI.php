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
 * Workspace access handler table GUI class
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.de>
 */
class ilWorkspaceAccessTableGUI extends ilTable2GUI
{
    protected int $node_id;
    protected ilWorkspaceAccessHandler $handler;

    /**
     * Constructor
     *
     * @param object $a_parent_obj parent gui object
     * @param string $a_parent_cmd parent default command
     * @param int $a_node_id current workspace object
     * @param ilWorkspaceAccessHandler $a_handler workspace access handler
     */
    public function __construct(
        object $a_parent_obj,
        string $a_parent_cmd,
        int $a_node_id,
        ilWorkspaceAccessHandler $a_handler
    ) {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->lng = $DIC->language();
        $ilCtrl = $DIC->ctrl();
        $lng = $DIC->language();

        $this->node_id = $a_node_id;
        $this->handler = $a_handler;

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->setId("il_tbl_wsacl");

        $this->setTitle($lng->txt("wsp_shared_table_title"));
                
        $this->addColumn($this->lng->txt("wsp_shared_with"), "title");
        $this->addColumn($this->lng->txt("details"), "type");
        $this->addColumn($this->lng->txt("actions"));
        
        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");

        $this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
        $this->setRowTemplate("tpl.access_row.html", "Services/PersonalWorkspace");

        $this->importData();
    }

    /**
     * Import data from DB
     */
    protected function importData() : void
    {
        $data = array();
        $caption = "";
        $type_txt = "";
        foreach ($this->handler->getPermissions($this->node_id) as $obj_id) {
            // title is needed for proper sorting
            // special modes should always be on top!
            $title = null;
            
            switch ($obj_id) {
                case ilWorkspaceAccessGUI::PERMISSION_REGISTERED:
                    $caption = $this->lng->txt("wsp_set_permission_registered");
                    $title = "0" . $caption;
                    break;
                
                case ilWorkspaceAccessGUI::PERMISSION_ALL_PASSWORD:
                    $caption = $this->lng->txt("wsp_set_permission_all_password");
                    $title = "0" . $caption;
                    break;
                
                case ilWorkspaceAccessGUI::PERMISSION_ALL:
                    $caption = $this->lng->txt("wsp_set_permission_all");
                    $title = "0" . $caption;
                    break;
                                                
                default:
                    $type = ilObject::_lookupType($obj_id);
                    $type_txt = $this->lng->txt("obj_" . $type);
                    
                    if ($type === null) {
                        // invalid object/user
                    } elseif ($type != "usr") {
                        $title = $caption = ilObject::_lookupTitle($obj_id);
                    } else {
                        $caption = ilUserUtil::getNamePresentation($obj_id, false, true);
                        $title = strip_tags($caption);
                    }
                    break;
            }
            
            if ($title) {
                $data[] = array("id" => $obj_id,
                    "title" => $title,
                    "caption" => $caption,
                    "type" => $type_txt);
            }
        }
    
        $this->setData($data);
    }
    
    /**
     * Fill table row
     * @param array $a_set data array
     */
    protected function fillRow(array $a_set) : void
    {
        $ilCtrl = $this->ctrl;
        
        // properties
        $this->tpl->setVariable("TITLE", $a_set["caption"]);
        $this->tpl->setVariable("TYPE", $a_set["type"]);

        $ilCtrl->setParameter($this->parent_obj, "obj_id", $a_set["id"]);
        $this->tpl->setVariable(
            "HREF_CMD",
            $ilCtrl->getLinkTarget($this->parent_obj, "removePermission")
        );
        $this->tpl->setVariable("TXT_CMD", $this->lng->txt("remove"));
    }
}
