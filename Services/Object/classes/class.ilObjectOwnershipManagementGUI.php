<?php declare(strict_types=1);

/* Copyright (c) 1998-2021 ILIAS open source, GPLv3, see LICENSE */

/**
 * Class ilObjectOwnershipManagementGUI
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 *
 * @ilCtrl_Calls ilObjectOwnershipManagementGUI:
 */
class ilObjectOwnershipManagementGUI
{
    protected ilObjUser $user;
    protected ilCtrl $ctrl;
    protected ilGlobalTemplateInterface $tpl;
    protected ilToolbarGUI $toolbar;
    protected ilLanguage $lng;
    protected ilObjectDefinition $obj_definition;
    protected ilTree $tree;

    protected int $user_id;
    
    public function __construct(int $user_id = null)
    {
        global $DIC;

        $this->user = $DIC->user();
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC["tpl"];
        $this->toolbar = $DIC->toolbar();
        $this->lng = $DIC->language();
        $this->obj_definition = $DIC["objDefinition"];
        $this->tree = $DIC->repositoryTree();

        $this->user_id = $this->user->getId();
        if (!is_null($user_id)) {
            $this->user_id = $user_id;
        }
    }
    
    public function executeCommand() : void
    {
        $this->ctrl->getNextClass($this);
        $cmd = $this->ctrl->getCmd();

        if (!$cmd) {
            $cmd = "listObjects";
        }
        $this->$cmd();
    }
    
    public function listObjects() : void
    {
        $sel_type = '';

        $objects = ilObject::getAllOwnedRepositoryObjects($this->user_id);
        
        if (sizeof($objects)) {
            $this->toolbar->setFormAction($this->ctrl->getFormAction($this, "listObjects"));
            
            $sel = new ilSelectInputGUI($this->lng->txt("type"), "type");
            $this->toolbar->addStickyItem($sel, true);
            
            $button = ilSubmitButton::getInstance();
            $button->setCaption("ok");
            $button->setCommand("listObjects");
            $this->toolbar->addStickyItem($button);

            $options = [];
            foreach (array_keys($objects) as $type) {
                if (!$this->obj_definition->isPlugin($type)) {
                    $options[$type] = $this->lng->txt("obj_" . $type);
                } else {
                    $options[$type] = ilObjectPlugin::lookupTxtById($type, "obj_" . $type);
                }
            }
            asort($options);
            $sel->setOptions($options);

            $sel_type = (string) $_REQUEST["type"];
            if ($sel_type) {
                $sel->setValue($sel_type);
            } else {
                $sel_type = array_keys($options);
                $sel_type = array_shift($sel_type);
            }
            $this->ctrl->setParameter($this, "type", $sel_type);
        }

        if (is_array($objects[$sel_type]) && sizeof($objects[$sel_type])) {
            ilObject::fixMissingTitles($sel_type, $objects[$sel_type]);
        }
        
        $tbl = new ilObjectOwnershipManagementTableGUI($this, "listObjects", $this->user_id, $objects[$sel_type]);
        $this->tpl->setContent($tbl->getHTML());
    }
    
    public function applyFilter() : void
    {
        $tbl = new ilObjectOwnershipManagementTableGUI($this, "listObjects", $this->user_id);
        $tbl->resetOffset();
        $tbl->writeFilterToSession();
        $this->listObjects();
    }
    
    public function resetFilter() : void
    {
        $tbl = new ilObjectOwnershipManagementTableGUI($this, "listObjects", $this->user_id);
        $tbl->resetOffset();
        $tbl->resetFilter();
        $this->listObjects();
    }
    
    protected function redirectParentCmd(int $ref_id, string $cmd) : void
    {
        $parent = $this->tree->getParentId($ref_id);
        $this->ctrl->setParameterByClass("ilRepositoryGUI", "ref_id", $parent);
        $this->ctrl->setParameterByClass("ilRepositoryGUI", "item_ref_id", $ref_id);
        $this->ctrl->setParameterByClass("ilRepositoryGUI", "cmd", $cmd);
        $this->ctrl->redirectByClass("ilRepositoryGUI");
    }
    
    protected function redirectCmd(int $ref_id, string $class, string $cmd = null) : void
    {
        $node = $this->tree->getNodeData($ref_id);
        $gui_class = "ilObj" . $this->obj_definition->getClassName($node["type"]) . "GUI";
        $path = ["ilRepositoryGUI", $gui_class, $class];
        
        if ($class == "ilExportGUI") {
            try {
                $this->ctrl->getLinkTargetByClass($path);
            } catch (Exception $e) {
                switch ($node["type"]) {
                    case "glo":
                        $export_cmd = "exportList";
                        $path = ["ilRepositoryGUI", "ilGlossaryEditorGUI", $gui_class];
                        break;

                    default:
                        $export_cmd = "export";
                        $path = ["ilRepositoryGUI", $gui_class];
                        break;
                }
                $this->ctrl->setParameterByClass($gui_class, "ref_id", $ref_id);
                $this->ctrl->setParameterByClass($gui_class, "cmd", $export_cmd);
                $this->ctrl->redirectByClass($path);
            }
        }
                        
        $this->ctrl->setParameterByClass($class, "ref_id", $ref_id);
        $this->ctrl->setParameterByClass($class, "cmd", $cmd);
        $this->ctrl->redirectByClass($path);
    }
    
    public function delete() : void
    {
        $ref_id = (int) $_REQUEST["ownid"];
        $this->redirectParentCmd($ref_id, "delete");
    }
    
    public function move() : void
    {
        $ref_id = (int) $_REQUEST["ownid"];
        $this->redirectParentCmd($ref_id, "cut");
    }
    
    public function export() : void
    {
        $ref_id = (int) $_REQUEST["ownid"];
        $this->redirectCmd($ref_id, "ilExportGUI");
    }
    
    public function changeOwner() : void
    {
        $ref_id = (int) $_REQUEST["ownid"];
        $this->redirectCmd($ref_id, "ilPermissionGUI", "owner");
    }
}
