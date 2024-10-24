<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("class.ilObjCloudGUI.php");
require_once("class.ilCloudFileNode.php");
require_once("class.ilCloudFileTree.php");
require_once("class.ilCloudConnector.php");

/**
 * Class ilCloudFileTree
 * Class for drawing the file tree.
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Martin Studer martin@fluxlabs.ch
 * @version $Id$
 * @extends ilCloudPluginGUI
 * @ingroup ModulesCloud
 */
class ilCloudPluginFileTreeGUI extends ilCloudPluginGUI
{

    /**
     * @var ilCloudFileTree
     */
    protected $file_tree;

    /**
     * @param ilCloudFileTree $file_tree
     */
    public function __construct($plugin_service_class, ilCloudFileTree $file_tree)
    {
        parent::__construct($plugin_service_class);
        $this->setFileTree($file_tree);
    }

    /**
     * @param ilCloudFileTree $file_tree
     */
    public function setFileTree(ilCloudFileTree $file_tree)
    {
        if ($file_tree) {
            $this->file_tree = $file_tree;
        }
    }

    public function getFileTree() : ilCloudFileTree
    {
        return $this->file_tree;
    }

    public function getFolderHtml(
        ilObjCloudGUI $gui_class,
        int $id,
        bool $delete_files = false,
        bool $delete_folder = false,
        bool $download = false,
        bool $files_visible = false,
        bool $folders_visible = false
    ) : string {
        global $DIC;
        $lng = $DIC['lng'];

        $node = $this->getFileTree()->getNodeFromId($id);
        if (!$node) {
            throw new ilCloudException(ilCloudException::ID_DOES_NOT_EXIST_IN_FILE_TREE_IN_SESSION, $id);
        }
        $tree_tpl = new ilTemplate("tpl.cloud_block.html", true, true, "Modules/Cloud/");

        if ($files_visible || $folders_visible) {
            $tree_tpl->setVariable("NODE_ID", $node->getId());

            $block = new ilTemplate("tpl.container_list_block.html", true, true, "Services/Container/");

            if ($node->hasChildren()) {
                $block->setVariable("BLOCK_HEADER_CONTENT", $lng->txt("content"));

                $children = $this->getFileTree()->getSortedListOfChildren($node);
                foreach ($children as $path) {
                    $child_node = $this->getFileTree()->getNodeFromPath($path);
                    if (($child_node->getIsDir() && $folders_visible) || (!$child_node->getIsDir() && $files_visible)) {
                        $block->setCurrentBlock("container_standard_row");
                        if ($child_node->getIsDir()) {
                            $block->setVariable("ROW_ID", "id=xcld_folder_" . $child_node->getId());
                        } else {
                            $block->setVariable("ROW_ID", "id=xcld_file_" . $child_node->getId());
                        }
                        $block->setVariable("BLOCK_ROW_CONTENT",
                            $this->getItemHtml($child_node, $gui_class, $delete_files, $delete_folder, $download));
                        $block->parseCurrentBlock();
                    }
                }
            }
            $this->setBlockVariablePlugin($block);
            $tree_tpl->setVariable("CONTENT", $block->get());
        } else {
            // Nothing is visible
            // $tree_tpl->setVariable("CONTENT", $lng->txt("file_folder_not_visible"));
        }
        $this->setTreeVariablePlugin($tree_tpl, $gui_class, $id, $delete_files, $delete_folder, $download,
            $files_visible, $folders_visible);

        return $tree_tpl->get();
    }

    public function setTreeVariablePlugin(
        ilTemplate $tree_tpl,
        ilObjCloudGUI $gui_class,
        int $id,
        bool $delete_files = false,
        bool $delete_folder = false,
        bool $download = false,
        bool $files_visible = false,
        bool $folders_visible = false
    ) : void {
    }

    protected function setBlockVariablePlugin(ilTemplate $block) : void
    {
    }

    public function getItemHtml(
        ilCloudFileNode $node,
        ilObjCloudGUI $gui_class,
        bool $delete_files = false,
        bool $delete_folder = false,
        bool $download = false
    ) : string {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];

        $item = new ilTemplate("tpl.container_list_item.html", true, true, "Services/Container/");

        $action_list_gui = ilCloudConnector::getActionListGUIClass($this->getService());
        $item->setVariable("COMMAND_SELECTION_LIST",
            $action_list_gui->getSelectionListItemsHTML($delete_files, $delete_folder, $node));

        $item->setVariable("DIV_CLASS", "ilContainerListItemOuter");
        $item->touchBlock("d_1");

        require_once('./Services/Calendar/classes/class.ilDate.php');
        $modified = ilDatePresentation::formatDate(new ilDateTime($node->getModified(), IL_CAL_UNIX));

        if ($node->getIconPath() != "") {
            $item->setVariable("SRC_ICON", $node->getIconPath());
        }

        // Folder with content
        if ($node->getIsDir()) {
            if ($node->getIconPath() == "") {
                //				$item->setVariable("SRC_ICON", "./Modules/Cloud/templates/images/icon_folder_b.png");
                $item->setVariable("SRC_ICON", ilUtil::getImagePath('icon_dcl_fold.svg'));
            }
            $item->setVariable("TXT_TITLE_LINKED", basename($node->getPath()));
            $item->setVariable("HREF_TITLE_LINKED", $this->getLinkToFolder($node));
        } // File
        else {
            if ($node->getIconPath() == "") {
                //				$item->setVariable("SRC_ICON", "./Modules/Cloud/templates/images/icon_file_b.png");
                $item->setVariable("SRC_ICON", ilUtil::getImagePath('icon_dcl_file.svg'));
            }

            $item->setVariable(
                "TXT_DESC",
                $this->formatBytes($node->getSize()) . "&nbsp;&nbsp;&nbsp;" . $modified
            );
            if ($download) {
                $item->setVariable("TXT_TITLE_LINKED", basename($node->getPath()));
                $item->setVariable("HREF_TITLE_LINKED",
                    $ilCtrl->getLinkTarget($gui_class, "getFile") . "&id=" . $node->getId());
            } else {
                $item->setVariable("TXT_TITLE", basename($node->getPath()));
            }
        }

        $this->setItemVariablePlugin($item, $node);

        return $item->get();
    }

    protected function formatBytes(int $bytes, int $precision = 2) : string
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, $precision) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, $precision) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, $precision) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    protected function setItemVariablePlugin(ilTemplate $item, ilCloudFileNode $node) : void
    {
    }

    public function getLocatorHtml(ilCloudFileNode $node) : string
    {
        static $ilLocator;

        if ($node == $this->getFileTree()->getRootNode()) {
            $ilLocator = new ilLocatorGUI();
            $ilLocator->addItem($this->getPluginObject()->getCloudModulObject()->getTitle(),
                ilCloudPluginFileTreeGUI::getLinkToFolder($node));
        } else {
            $this->getLocatorHtml($this->getFileTree()->getNodeFromId($node->getParentId()));
            $ilLocator->addItem(basename($node->getPath()), $this->getLinkToFolder($node));
        }

        return "<DIV class='xcld_locator' id='xcld_locator_" . $node->getId() . "'>" . $ilLocator->getHTML() . "</DIV>";
    }

    public static function getLinkToFolder(ilCloudFileNode $node) : string
    {
        return "#/open_folder?id_parent=" . $node->getParentId() . "&current_id=" . $node->getId() . "&current_path=" . self::_urlencode($node->getPath());
    }

    protected function addDropZone() : void
    {
        $options = new stdClass();
        $options->dropZone = ".ilFileUploadDropZone_1";
        $options->fileInput = "#ilFileUploadInput_1";
        $options->submitButton = "uploadFiles";
        $options->cancelButton = "cancelAll";
        $options->dropArea = ".ilFileDropTarget";
        $options->fileList = "#ilFileUploadList_1";
        $options->fileSelectButton = "#ilFileUploadFileSelect_1";
        echo "<script language='javascript' type='text/javascript'>var fileUpload1 = new ilFileUpload(1, " . ilJsonUtil::encode($options)
            . ");</script>";
    }

    /**
     * urlencode without encoding slashes
     */
    protected static function _urlencode(string $str) : string
    {
        return str_replace('%2F', '/', rawurlencode($str));
    }
}
