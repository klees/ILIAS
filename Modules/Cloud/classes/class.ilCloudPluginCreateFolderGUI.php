<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("./Services/JSON/classes/class.ilJsonUtil.php");
require_once("./Modules/Cloud/exceptions/class.ilCloudException.php");

/**
 * Class ilCloudPluginCreateFolderGUI
 * Standard GUI when creating a new folder. Could be overwritten by the plugin if needed.
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Martin Studer martin@fluxlabs.ch
 * @version $Id:
 * @extends ilCloudPluginGUI
 * @ingroup ModulesCloud
 */
class ilCloudPluginCreateFolderGUI extends ilCloudPluginGUI
{
    public function asyncCreateFolder()
    {
        global $DIC;
        $tpl = $DIC['tpl'];
        $response = new stdClass();
        $response->success = null;
        $response->error = null;
        $response->message = null;

        try {
            $this->initCreateFolder();
            $response->content = $this->form->getHTML();
            $response->success = true;
        } catch (Exception $e) {
            $response->message = ilUtil::getSystemMessageHTML($e->getMessage(), "failure");
        }
        header('Content-type: application/json');
        echo ilJsonUtil::encode($response);
        exit;
    }

    public function initCreateFolder() : void
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $lng = $DIC['lng'];

        require_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
        $this->form = new ilPropertyFormGUI();
        $this->form->setId("cld_create_folder");

        $name = new ilTextInputGUI($lng->txt("cld_folder_name"), "folder_name");
        $name->setRequired(true);
        $this->form->addItem($name);

        // folder id
        $id = new ilHiddenInputGUI("parent_folder_id");
        $id->setValue($_POST["id"]);
        $this->form->addItem($id);

        $this->form->addCommandButton("createFolder", $lng->txt("cld_create_folder"));
        $this->form->addCommandButton("cancel", $lng->txt("cancel"));

        $this->form->setTitle($lng->txt("cld_create_folder"));
        $this->form->setFormAction($ilCtrl->getFormAction($this));
        $this->form->setTarget("cld_blank_target");
    }

    public function createFolder() : void
    {
        global $DIC;
        $tpl = $DIC['tpl'];
        $lng = $DIC['lng'];

        $response = new stdClass();
        $response->success = null;
        $response->message = null;
        $response->folder_id = null;
        try {
            $response->status = "done";
            require_once("class.ilCloudFileTree.php");
            $file_tree = ilCloudFileTree::getFileTreeFromSession();
            $new_node = $file_tree->addFolderToService($_POST["parent_folder_id"], $_POST["folder_name"]);
            $response->folder_id = $new_node->getId();
            $response->folder_path = $new_node->getPath();
            $response->success = true;
            $response->message = ilUtil::getSystemMessageHTML($lng->txt("cld_folder_created"), "success");
        } catch (Exception $e) {
            $response->message = ilUtil::getSystemMessageHTML($e->getMessage(), "failure");
        }
        echo "<script language='javascript' type='text/javascript'>window.parent.il.CloudFileList.afterCreateFolder(" . ilJsonUtil::encode($response) . ");</script>";
        exit;
    }

    public function cancel() : void
    {
        $response = new stdClass();
        $response->status = "cancel";

        echo "<script language='javascript' type='text/javascript'>window.parent.il.CloudFileList.afterCreateFolder(" . ilJsonUtil::encode($response) . ");</script>";
        exit;
    }
}
