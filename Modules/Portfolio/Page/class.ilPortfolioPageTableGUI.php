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
 * Portfolio page table
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 */
class ilPortfolioPageTableGUI extends ilTable2GUI
{
    protected array $blogs;
    protected ilObjUser $user;
    protected ilObjPortfolio $portfolio;
    protected bool $is_template;
    protected string $page_gui;
    
    public function __construct(
        ilObjPortfolioBaseGUI $a_parent_obj,
        string $a_parent_cmd
    ) {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->lng = $DIC->language();
        $this->user = $DIC->user();
        $ilCtrl = $DIC->ctrl();
        $lng = $DIC->language();

        parent::__construct($a_parent_obj, $a_parent_cmd);
        $this->disable("numinfo");
        $this->setLimit(9999);

        /** @var ilObjPortfolio $portfolio */
        $portfolio = $a_parent_obj->getObject();
        $this->portfolio = $portfolio;
        $this->page_gui = $this->parent_obj->getPageGUIClassName();
        $this->is_template = ($this->portfolio->getType() == "prtt");
        
        $this->setTitle($lng->txt("tabs"));

        //$this->addColumn($this->lng->txt(""), "", "1");
        $this->addColumn($this->lng->txt("user_order"));
        $this->addColumn($this->lng->txt("title"));
        $this->addColumn($this->lng->txt("type"));
        $this->addColumn($this->lng->txt("actions"));

        $this->setFormAction($ilCtrl->getFormAction($a_parent_obj));
        $this->setRowTemplate("tpl.portfolio_page_row.html", "Modules/Portfolio");

        //$this->addMultiCommand("confirmPortfolioPageDeletion", $lng->txt("delete"));
        //$this->addMultiCommand("copyPageForm", $lng->txt("prtf_copy_page"));
        
        $this->addCommandButton(
            "savePortfolioPagesOrdering",
            $lng->txt("user_save_ordering_and_titles")
        );

        $this->getItems();
                
        $lng->loadLanguageModule("blog");
    }

    public function getItems() : void
    {
        $ilUser = $this->user;
            
        $data = ilPortfolioPage::getAllPortfolioPages($this->portfolio->getId());
        $this->setData($data);
                
        if (!$this->is_template) {
            $this->blogs = array();
            $tree = new ilWorkspaceTree($ilUser->getId());
            $root = $tree->readRootId();
            if ($root) {
                $root = $tree->getNodeData($root);
                foreach ($tree->getSubTree($root) as $node) {
                    if ($node["type"] == "blog") {
                        $this->blogs[$node["obj_id"]] = $node["wsp_id"];
                    }
                }
            }
        }
    }

    protected function fillRow(array $a_set) : void
    {
        $lng = $this->lng;
        $ilCtrl = $this->ctrl;
        $action_items = [];

        switch ($a_set["type"]) {
            case ilPortfolioPage::TYPE_PAGE:
                $this->tpl->setCurrentBlock("title_field");
                $this->tpl->setVariable("ID", $a_set["id"]);
                $this->tpl->setVariable("VAL_TITLE", ilLegacyFormElementsUtil::prepareFormOutput($a_set["title"]));
                $this->tpl->parseCurrentBlock();

                $ilCtrl->setParameterByClass(
                    $this->page_gui,
                    "ppage",
                    $a_set["id"]
                );
                $action_item = ilLinkButton::getInstance();
                $action_item->setCaption('edit_page');
                $action_item->setUrl($ilCtrl->getLinkTargetByClass($this->page_gui, "edit"));
                $action_items[] = $action_item;


                $this->tpl->setVariable("TYPE", $lng->txt("page"));
                break;
            
            case ilPortfolioPage::TYPE_BLOG:
                if (!$this->is_template) {
                    $this->tpl->setCurrentBlock("title_static");
                    $this->tpl->setVariable("VAL_TITLE_STATIC", ilObjBlog::_lookupTitle($a_set["title"]));
                    $this->tpl->parseCurrentBlock();

                    $obj_id = (int) $a_set["title"];
                    if (isset($this->blogs[$obj_id])) {
                        $node_id = $this->blogs[$obj_id];
                        $link = ilWorkspaceAccessHandler::getGotoLink($node_id, $obj_id);

                        // #11519
                        $ilCtrl->setParameterByClass(
                            $this->page_gui,
                            "ppage",
                            $a_set["id"]
                        );
                        $link = $ilCtrl->getLinkTargetByClass(array($this->page_gui, "ilobjbloggui"), "render");

                        $action_item = ilLinkButton::getInstance();
                        $action_item->setCaption('blog_edit');
                        $action_item->setUrl($link);
                        $action_items[] = $action_item;
                    }
                    $this->tpl->setVariable("TYPE", $lng->txt("obj_blog"));
                }
                break;
                
            case ilPortfolioTemplatePage::TYPE_BLOG_TEMPLATE:
                if ($this->is_template) {
                    $this->tpl->setCurrentBlock("title_field");
                    $this->tpl->setVariable("ID", $a_set["id"]);
                    $this->tpl->setVariable("VAL_TITLE", ilLegacyFormElementsUtil::prepareFormOutput($a_set["title"]));
                    $this->tpl->parseCurrentBlock();
                    
                    $this->tpl->setCurrentBlock("title_static");
                    //$this->tpl->setVariable("VAL_TITLE_STATIC", $lng->txt("obj_blog"));
                    $this->tpl->parseCurrentBlock();
                    $this->tpl->setVariable("TYPE", $lng->txt("obj_blog"));
                }
                break;
        }

        $ilCtrl->setParameter($this->parent_obj, "prtf_page", $a_set["id"]);

        // copy
        $action_item = ilLinkButton::getInstance();
        $action_item->setCaption('prtf_copy_tab');
        $action_item->setUrl($ilCtrl->getLinkTarget($this->parent_obj, "copyPageForm"));
        $action_items[] = $action_item;

        // delete
        $action_item = ilLinkButton::getInstance();
        $action_item->setCaption('delete');
        $action_item->setUrl($ilCtrl->getLinkTarget($this->parent_obj, "confirmPortfolioPageDeletion"));
        $action_items[] = $action_item;

        $ilCtrl->setParameter($this->parent_obj, "prtf_page", "");

        if (count($action_items) > 0) {
            $split_button = ilSplitButtonGUI::getInstance();
            $i = 0;
            foreach ($action_items as $item) {
                if ($i++ == 0) {
                    $split_button->setDefaultButton($item);
                } else {
                    $split_button->addMenuItem(new ilButtonToSplitButtonMenuItemAdapter($item));
                }
            }
            $this->tpl->setVariable("SPLIT_BUTTON", $split_button->render());
        }


        $this->tpl->setVariable("ID", $a_set["id"]);
        $this->tpl->setVariable("VAL_ORDER_NR", $a_set["order_nr"]);
    }
}
