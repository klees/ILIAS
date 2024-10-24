<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Modules/Cloud/classes/class.ilCloudPluginListGUI.php');

/**
 * Class ilCloudPluginActionListGUI
 * Basic functionality of the action list. Can be extended to define addidtional actions by the plugin. Note that
 * the list is loaded asyncronically by default. Disable if not wanted.
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Martin Studer martin@fluxlabs.ch
 * @version $Id:
 * @extends ilCloudPluginListGUI
 * @ingroup ModulesCloud
 */
class ilCloudPluginActionListGUI extends ilCloudPluginListGUI
{
    protected ilAdvancedSelectionListGUI $selection_list;
    protected ?ilcloudFileNode $node = null;

    public function getSelectionListItemsHTML(bool $delete_item = false, bool $delete_folder = false, ilCloudFileNode $node): string
    {
        global $DIC;
        $lng = $DIC['lng'];
        $ilCtrl = $DIC['ilCtrl'];

        /**
         * @var ilCtrl $ilCtrl
         */

        $this->node = $node;

        if (($delete_item && !$node->getIsDir()) || ($delete_folder && $node->getIsDir()) || $this->checkHasAction()) {
            require_once("./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php");
            $this->selection_list = new ilAdvancedSelectionListGUI();
            $this->selection_list->setId("id_action_list_" . $this->node->getId());
            $this->selection_list->setListTitle($lng->txt("actions"));
            $this->selection_list->setItemLinkClass("xsmall");

            if ($this->getAsyncMode()) {
                $this->selection_list->setAsynch(true);
                $this->selection_list->setAsynchUrl(html_entity_decode($ilCtrl->getLinkTargetByClass("ilobjcloudgui",
                        "asyncGetActionListContent", false)
                    . "&node_id=" . $node->getId()));
            } else {
                $this->addSelectionListItems($delete_item, $delete_folder);
            }

            return $this->selection_list->getHtml();
        } else {
            return "";
        }
    }

    protected function addSelectionListItems(bool $delete_item, bool $delete_folder): void
    {
        global $DIC;
        $lng = $DIC['lng'];
        $this->addItemsBefore();
        if (($delete_item && !$this->node->getIsDir()) || ($delete_folder && $this->node->getIsDir())) {
            $this->selection_list->addItem($lng->txt("delete"), "delete_item",
                "javascript:il.CloudFileList.deleteItem('" . $this->node->getId()
                . "');");
        }

        $this->addItemsAfter();
    }

    public function asyncGetContent(bool $delete_item = false, bool $delete_folder = false, ilCloudFileNode $node): void
    {
        global $DIC;
        $lng = $DIC['lng'];
        $this->node = $node;
        require_once("./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php");
        $this->selection_list = new ilAdvancedSelectionListGUI();
        $this->selection_list->setId($this->node->getId());
        $this->selection_list->setListTitle($lng->txt("actions"));
        $this->selection_list->setItemLinkClass("xsmall");

        $this->addSelectionListItems($delete_item, $delete_folder);
        if ($this->selection_list->getItems() != null) {
            echo $this->selection_list->getHTML(true);
            exit;
        } else {
            echo $lng->txt("empty");
            exit;
        }
    }

    protected function addItemsBefore(): void
    {
    }

    protected function addItemsAfter(): void
    {
    }

    protected function checkHasAction(): void
    {
    }

    protected function getAsyncMode(): bool
    {
        return true;
    }
}
