<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once "./Services/Object/classes/class.ilObjectGUI.php" ;

/**
 * Web Resource Administration Settings.
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @version $Id:$
 *
 * @ilCtrl_Calls ilObjWebResourceAdministrationGUI: ilPermissionGUI
 *
 * @ingroup ModulesWebResource
 */
class ilObjWebResourceAdministrationGUI extends ilObjectGUI
{
    public function __construct($a_data, $a_id, $a_call_by_reference = true, $a_prepare_output = true)
    {
        $this->type = "wbrs";
        parent::__construct($a_data, $a_id, $a_call_by_reference, $a_prepare_output);

        $this->lng->loadLanguageModule("webr");
    }

    public function executeCommand() : void
    {
        $next_class = $this->ctrl->getNextClass($this);
        $cmd = $this->ctrl->getCmd();

        $this->prepareOutput();

        if (!$this->rbac_system->checkAccess("visible,read", $this->object->getRefId())) {
            $this->error->raiseError($this->lng->txt("no_permission"), $this->error->WARNING);
        }

        switch ($next_class) {
            case 'ilpermissiongui':
                $this->tabs_gui->setTabActive("perm_settings");
                include_once "Services/AccessControl/classes/class.ilPermissionGUI.php";
                $perm_gui = new ilPermissionGUI($this);
                $this->ctrl->forwardCommand($perm_gui);
                break;

            default:
                if (!$cmd || $cmd == "view") {
                    $cmd = "editSettings";
                }
                $this->$cmd();
                break;
        }
    }

    public function getAdminTabs() : void
    {
        global $DIC;

        $rbacsystem = $DIC['rbacsystem'];

        if ($rbacsystem->checkAccess("visible,read", $this->object->getRefId())) {
            $this->tabs_gui->addTarget(
                "settings",
                $this->ctrl->getLinkTarget($this, "editSettings"),
                array("editSettings", "view")
            );
        }

        if ($rbacsystem->checkAccess("edit_permission", $this->object->getRefId())) {
            $this->tabs_gui->addTarget(
                "perm_settings",
                $this->ctrl->getLinkTargetByClass("ilpermissiongui", "perm"),
                array(),
                "ilpermissiongui"
            );
        }
    }
    
    public function editSettings(ilObjPropertyFormGUI $a_form = null)
    {
        $this->tabs_gui->setTabActive('settings');
                
        if (!$a_form) {
            $a_form = $this->initFormSettings();
        }
        $this->tpl->setContent($a_form->getHTML());
        return true;
    }

    public function saveSettings()
    {
        global $DIC;

        $ilSetting = $DIC['ilSetting'];
        
        $this->checkPermission("write");
        
        $form = $this->initFormSettings();
        if ($form->checkInput()) {
            $ilSetting->set("links_dynamic", $form->getInput("links_dynamic"));
            
            $this->tpl->setOnScreenMessage('success', $this->lng->txt("settings_saved"), true);
            $this->ctrl->redirect($this, "editSettings");
        }
        
        $form->setValuesByPost();
        $this->editSettings($form);
    }

    protected function initFormSettings()
    {
        global $DIC;

        $ilSetting = $DIC['ilSetting'];
        $ilAccess = $DIC['ilAccess'];
        
        include_once "Services/Form/classes/class.ilPropertyFormGUI.php";
        $form = new ilPropertyFormGUI();
        $form->setFormAction($this->ctrl->getFormAction($this, "saveSettings"));
        $form->setTitle($this->lng->txt("settings"));
        
        // dynamic web links
        $cb = new ilCheckboxInputGUI($this->lng->txt("links_dynamic"), "links_dynamic");
        $cb->setInfo($this->lng->txt("links_dynamic_info"));
        $cb->setChecked($ilSetting->get("links_dynamic"));
        $form->addItem($cb);
    
        if ($ilAccess->checkAccess("write", '', $this->object->getRefId())) {
            $form->addCommandButton("saveSettings", $this->lng->txt("save"));
            $form->addCommandButton("view", $this->lng->txt("cancel"));
        }

        return $form;
    }
}
