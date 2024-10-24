<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
require_once("./Modules/Cloud/exceptions/class.ilCloudException.php");

/**
 * Class ilCloudPluginDeleteGUI
 * Standard GUI when deleting files or folders. Could be overwritten by the plugin if needed.
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Martin Studer martin@fluxlabs.ch
 * @version $Id:
 * @extends ilCloudPluginGUI
 * @ingroup ModulesCloud
 */
class ilCloudPluginDeleteGUI extends ilCloudPluginGUI
{
    protected string $path = "/";
    protected int $id = 0;
    protected bool $is_dir;
    protected ilConfirmationGUI $gui;

    /**
     * is called async and prints the content from the confirmation gui
     */
    public function asyncDeleteItem(): void
    {
        global $DIC;
        $tpl = $DIC['tpl'];
        $lng = $DIC['lng'];
        $response = new stdClass();
        $response->success = null;
        $response->message = null;
        $response->content = null;
        $file_tree = ilCloudFileTree::getFileTreeFromSession();
        try {
            $node = $file_tree->getNodeFromId($_POST["id"]);
            if (!$node) {
                throw new ilCloudException(ilCloudException::ID_DOES_NOT_EXIST_IN_FILE_TREE_IN_SESSION);
            } else {
                $this->is_dir = $node->getIsDir();
            }

            $this->path = $node->getPath();
            $this->id = $node->getId();
            if (!$this->is_dir) {
                $this->path = rtrim($this->path, "/");
            }
            $this->initDeleteItem();
            $response->content = "<div id = 'cld_delete_item' >";
            if ($this->is_dir) {
                $response->content .= ilUtil::getSystemMessageHTML($lng->txt("cld_confirm_delete_folder"), "question");
            } else {
                $response->content .= ilUtil::getSystemMessageHTML($lng->txt("cld_confirm_delete_file"), "question");
            }
            $response->content .= $this->gui->getHTML();
            $response->content .= "</div >";
            $response->success = true;
        } catch (Exception $e) {
            $response->message = ilUtil::getSystemMessageHTML($e->getMessage(), "failure");
        }
        header('Content-type: application/json');
        echo ilJsonUtil::encode($response);
        exit;
    }

    public function initDeleteItem(): void
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $lng = $DIC['lng'];

        $this->gui = new ilConfirmationTableGUI(true);
        $this->gui->setFormName("cld_delete_item");
        $this->gui->getTemplateObject()->setVariable("ACTIONTARGET", "cld_blank_target");

        $this->gui->addCommandButton('deleteItem', $lng->txt('confirm'));
        $this->gui->addCommandButton('cancel', $lng->txt('cancel'));
        $this->gui->setFormAction($ilCtrl->getFormAction($this));

        if ($this->is_dir) {
            $item[] = array(
                "var" => 'id',
                "id" => $this->id,
                "text" => basename($this->path),
                "img" => ilUtil::getImagePath('icon_dcl_fold.svg'),
            );
        } else {
            $item[] = array(
                "var" => 'id',
                "id" => $this->id,
                "text" => basename($this->path),
                "img" => ilUtil::getImagePath('icon_dcl_file.svg'),
            );
        }
        $this->gui->setData($item);
    }

    public function deleteItem(): void
    {
        global $DIC;
        $tpl = $DIC['tpl'];
        $lng = $DIC['lng'];

        $response = new stdClass();
        $response->success = null;
        $response->message = null;

        if (true) {
            try {
                $file_tree = ilCloudFileTree::getFileTreeFromSession();
                $node = $file_tree->getNodeFromId($_POST["id"]);
                $file_tree->deleteFromService($node->getId());
                $response->message = ilUtil::getSystemMessageHTML($lng->txt("cld_file_deleted"), "success");
                $response->success = true;
            } catch (Exception $e) {
                $response->message = ilUtil::getSystemMessageHTML($e->getMessage(), "failure");
            }
        }
        echo "<script language='javascript' type='text/javascript'>window.parent.il.CloudFileList.afterDeleteItem(" . ilJsonUtil::encode($response)
            . ");</script>";
        exit;
    }

    public function cancel(): void
    {
        $response = new stdClass();
        $response->status = "cancel";

        echo "<script language='javascript' type='text/javascript'>window.parent.il.CloudFileList.afterDeleteItem(" . ilJsonUtil::encode($response)
            . ");</script>";
        exit;
    }
}
