<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilObjDataCollectionGUI
 * @author       Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @author       Martin Studer <ms@studer-raimann.ch>
 * @author       Marcel Raimann <mr@studer-raimann.ch>
 * @author       Fabian Schmid <fs@studer-raimann.ch>
 * @author       Oskar Truffer <ot@studer-raimann.ch>
 * @author       Stefan Wanzenried <sw@studer-raimann.ch>
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilInfoScreenGUI, ilNoteGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilPermissionGUI, ilObjectCopyGUI, ilDclExportGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilDclTreePickInputGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilDclRecordListGUI, ilDclRecordEditGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilDclDetailedViewGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilDclTableListGUI, ilObjFileGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilObjUserGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilRatingGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilPropertyFormGUI
 * @ilCtrl_Calls ilObjDataCollectionGUI: ilDclPropertyFormGUI
 * @extends      ilObject2GUI
 */
class ilObjDataCollectionGUI extends ilObject2GUI
{
    const GET_DCL_GTR = "dcl_gtr";
    const GET_REF_ID = "ref_id";
    const GET_VIEW_ID = "tableview_id";

    public ?ilObject $object;

    /**
     * ilObjDataCollectionGUI constructor.
     * @param int $a_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     */
    public function __construct($a_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        global $DIC;

        parent::__construct($a_id, $a_id_type, $a_parent_node_id);

        $this->lng->loadLanguageModule("dcl");
        $this->lng->loadLanguageModule('content');
        $this->lng->loadLanguageModule('obj');
        $this->lng->loadLanguageModule('cntr');

        if (isset($_GET['table_id'])) {
            $this->table_id = $_GET['table_id'];
        } elseif (isset($_GET['tableview_id'])) {
            $this->table_id = ilDclTableView::find($_GET['tableview_id'])->getTableId();
        } elseif ($a_id > 0) {
            $this->table_id = $this->object->getFirstVisibleTableId();
        }
        /**
         * @var ilCtrl $ilCtrl
         */
        if (!$DIC->ctrl()->isAsynch()) {
            ilYuiUtil::initConnection();
            ilOverlayGUI::initJavascript();
            $DIC->ui()->mainTemplate()->addJavaScript('Modules/DataCollection/js/ilDataCollection.js');
            // # see  https://mantis.ilias.de/view.php?id=26463
            $DIC->ui()->mainTemplate()->addJavaScript("./Services/UIComponent/Modal/js/Modal.js");
            $DIC->ui()->mainTemplate()->addJavaScript("Modules/DataCollection/js/datacollection.js");
            $this->tpl->addOnLoadCode(
                "ilDataCollection.setEditUrl('" . $DIC->ctrl()->getLinkTargetByClass(
                    array(
                        'ilrepositorygui',
                        'ilobjdatacollectiongui',
                        'ildclrecordeditgui',
                    ),
                    'edit',
                    '',
                    true
                ) . "');"
            );
            $this->tpl->addOnLoadCode(
                "ilDataCollection.setCreateUrl('" . $DIC->ctrl()->getLinkTargetByClass(
                    array(
                        'ilrepositorygui',
                        'ilobjdatacollectiongui',
                        'ildclrecordeditgui',
                    ),
                    'create',
                    '',
                    true
                ) . "');"
            );
            $this->tpl->addOnLoadCode(
                "ilDataCollection.setSaveUrl('" . $DIC->ctrl()->getLinkTargetByClass(
                    array(
                        'ilrepositorygui',
                        'ilobjdatacollectiongui',
                        'ildclrecordeditgui',
                    ),
                    'save',
                    '',
                    true
                ) . "');"
            );
            $this->tpl->addOnLoadCode(
                "ilDataCollection.setDataUrl('" . $DIC->ctrl()->getLinkTargetByClass(
                    array(
                        'ilrepositorygui',
                        'ilobjdatacollectiongui',
                        'ildclrecordeditgui',
                    ),
                    'getRecordData',
                    '',
                    true
                ) . "');"
            );
        }
        $DIC->ctrl()->saveParameter($this, "table_id");
    }

    /**
     * @return string
     */
    public function getStandardCmd()
    {
        return "render";
    }

    public function getType() : string
    {
        return "dcl";
    }

    public function executeCommand() : void
    {
        global $DIC;

        $ilNavigationHistory = $DIC['ilNavigationHistory'];
        $ilHelp = $DIC['ilHelp'];
        $ilHelp->setScreenIdComponent('bibl');

        // Navigation History
        $link = $DIC->ctrl()->getLinkTarget($this, "render");

        if ($this->object != null) {
            $ilNavigationHistory->addItem($this->object->getRefId(), $link, "dcl");
        }

        // Direct-Link Resource, redirect to viewgui
        if ($_GET[self::GET_DCL_GTR]) {
            $DIC->ctrl()->setParameterByClass(ilDclDetailedViewGUI::class, 'tableview_id', $_GET[self::GET_VIEW_ID]);
            $DIC->ctrl()->setParameterByClass(ilDclDetailedViewGUI::class, 'record_id', $_GET[self::GET_DCL_GTR]);
            $DIC->ctrl()->redirectByClass(ilDclDetailedViewGUI::class, 'renderRecord');
        }

        $next_class = $DIC->ctrl()->getNextClass($this);
        $cmd = $this->ctrl->getCmd();

        if (!$this->getCreationMode() and $next_class != "ilinfoscreengui" and $cmd != 'infoScreen' and !$this->checkPermissionBool("read")) {
            $DIC->ui()->mainTemplate()->loadStandardTemplate();
            $DIC->ui()->mainTemplate()->setContent("Permission Denied.");

            return;
        }

        switch ($next_class) {
            case "ilinfoscreengui":
                $this->prepareOutput();
                $DIC->tabs()->activateTab("id_info");
                $this->infoScreenForward();
                break;

            case "ilcommonactiondispatchergui":
                $gui = ilCommonActionDispatcherGUI::getInstanceFromAjaxCall();
                $gui->enableCommentsSettings(false);
                $this->ctrl->forwardCommand($gui);
                break;

            case "ilpermissiongui":
                $this->prepareOutput();
                $DIC->tabs()->activateTab("id_permissions");
                $perm_gui = new ilPermissionGUI($this);
                $this->ctrl->forwardCommand($perm_gui);
                break;

            case "ilobjectcopygui":
                $cp = new ilObjectCopyGUI($this);
                $cp->setType("dcl");
                $DIC->ui()->mainTemplate()->loadStandardTemplate();
                $this->ctrl->forwardCommand($cp);
                break;

            case "ildcltablelistgui":
                $this->prepareOutput();
                $DIC->tabs()->setTabActive("id_tables");
                $tablelist_gui = new ilDclTableListGUI($this);
                $this->ctrl->forwardCommand($tablelist_gui);
                break;

            case "ildclrecordlistgui":
                $this->addHeaderAction();
                $this->prepareOutput();
                $DIC->tabs()->activateTab("id_records");
                $this->ctrl->setParameterByClass(ilDclRecordListGUI::class, 'tableview_id', $_REQUEST['tableview_id']);
                $recordlist_gui = new ilDclRecordListGUI($this, $this->table_id);
                $this->ctrl->forwardCommand($recordlist_gui);
                break;

            case "ildclrecordeditgui":
                $this->prepareOutput();
                $DIC->tabs()->activateTab("id_records");
                $recordedit_gui = new ilDclRecordEditGUI($this);
                $this->ctrl->forwardCommand($recordedit_gui);
                break;

            case "ilobjfilegui":
                $this->prepareOutput();
                $DIC->tabs()->setTabActive("id_records");
                $file_gui = new ilObjFile($this);
                $this->ctrl->forwardCommand($file_gui);
                break;

            case "ilratinggui":
                $rgui = new ilRatingGUI();
                $rgui->setObject($_GET['record_id'], "dcl_record", $_GET["field_id"], "dcl_field");
                $rgui->executeCommand();
                $DIC->ctrl()->redirectByClass("ilDclRecordListGUI", "listRecords");
                break;

            case "ildcldetailedviewgui":
                $this->prepareOutput();
                $recordview_gui = new ilDclDetailedViewGUI($this);
                $this->ctrl->forwardCommand($recordview_gui);
                $DIC->tabs()->clearTargets();
                $DIC->tabs()->setBackTarget($this->lng->txt("back"),
                    $DIC->ctrl()->getLinkTargetByClass(ilDclRecordListGUI::class,
                        ilDclRecordListGUI::CMD_LIST_RECORDS));
                break;

            case 'ilnotegui':
                $this->prepareOutput();
                $recordviewGui = new ilDclDetailedViewGUI($this);
                $this->ctrl->forwardCommand($recordviewGui);
                $DIC->tabs()->clearTargets();
                $DIC->tabs()->setBackTarget($this->lng->txt("back"), $DIC->ctrl()->getLinkTarget($this, ""));
                break;
            case "ildclexportgui":
                $this->prepareOutput();
                $DIC->tabs()->setTabActive("export");
                $exp_gui = new ilDclExportGUI($this);
                $exporter = new ilDclContentExporter($this->object->getRefId());
                $exp_gui->addFormat("xlsx", $this->lng->txt('dlc_xls_async_export'), $exporter, 'exportAsync');
                $exp_gui->addFormat("xml");

                $this->ctrl->forwardCommand($exp_gui);
                break;

            case strtolower(ilDclPropertyFormGUI::class):
                $recordedit_gui = new ilDclRecordEditGUI($this);
                $recordedit_gui->getRecord();
                $recordedit_gui->initForm();
                $form = $recordedit_gui->getForm();
                $this->ctrl->forwardCommand($form);
                break;

            default:
                switch ($cmd) {
                    case 'edit': // this is necessary because ilObjectGUI only calls its own editObject (why??)
                        $this->prepareOutput();
                        $this->editObject();
                        break;
                    default:
                        parent::executeCommand();
                }
        }
    }

    /**
     * this one is called from the info button in the repository
     * not very nice to set cmdClass/Cmd manually, if everything
     * works through ilCtrl in the future this may be changed
     */
    public function infoScreen()
    {
        $this->ctrl->setCmd("showSummary");
        $this->ctrl->setCmdClass("ilinfoscreengui");
        $this->infoScreenForward();
    }

    /**
     * show Content; redirect to ilDclRecordListGUI::listRecords
     */
    public function render()
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $this->ctrl->setParameterByClass('ilDclRecordListGUI', 'tableview_id', $_GET['tableview_id']);
        $ilCtrl->redirectByClass("ildclrecordlistgui", "show");
    }

    /**
     * show information screen
     */
    public function infoScreenForward()
    {
        global $DIC;
        $ilTabs = $DIC['ilTabs'];
        $ilErr = $DIC['ilErr'];

        $ilTabs->activateTab("id_info");

        if (!$this->checkPermissionBool("visible") && !$this->checkPermissionBool("read")) {
            $ilErr->raiseError($this->lng->txt("msg_no_perm_read"));
        }

        $info = new ilInfoScreenGUI($this);
        $info->enablePrivateNotes();
        $info->addMetaDataSections($this->object->getId(), 0, $this->object->getType());

        $this->ctrl->forwardCommand($info);
    }

    public function addLocatorItems() : void
    {
        global $DIC;
        $ilLocator = $DIC['ilLocator'];

        if (is_object($this->object)) {
            $ilLocator->addItem($this->object->getTitle(), $this->ctrl->getLinkTarget($this, ""), "", $this->node_id);
        }
    }

    /**
     * @param $a_target
     */
    public static function _goto($a_target)
    {
        global $DIC;
        $main_tpl = $DIC->ui()->mainTemplate();

        $ilAccess = $DIC->access();
        $lng = $DIC->language();

        $id = explode("_", $a_target);

        if ($ilAccess->checkAccess('read', "", $a_target)) {
            $_GET["baseClass"] = "ilRepositoryGUI";
            $_GET[self::GET_REF_ID] = $id[0];  // ref_id
            $_GET[self::GET_VIEW_ID] = $id[1]; // view_id
            $_GET[self::GET_DCL_GTR] = $id[2]; // record_id
            $_GET["cmd"] = "listRecords";
            require_once('./ilias.php');
            exit;
        }

        if ($ilAccess->checkAccess('visible', "", $a_target)) {
            ilObjectGUI::_gotoRepositoryNode($a_target, "infoScreen");
        }

        $main_tpl->setOnScreenMessage('failure', sprintf(
            $lng->txt("msg_no_perm_read_item"),
            ilObject::_lookupTitle(ilObject::_lookupObjId($a_target))
        ), true);
        ilObjectGUI::_gotoRepositoryRoot();
    }

    protected function initCreationForms(string $new_type) : array
    {
        $forms = parent::initCreationForms($new_type);

        return $forms;
    }

    protected function afterSave(ilObject $new_object) : void
    {
        $this->tpl->setOnScreenMessage('success', $this->lng->txt("object_added"), true);
        $this->ctrl->redirectByClass("ilDclTableListGUI", "listTables");
    }

    /**
     * setTabs
     * create tabs (repository/workspace switch)
     * this had to be moved here because of the context-specific permission tab
     */
    public function setTabs() : void
    {
        global $DIC;
        $ilAccess = $DIC['ilAccess'];
        $ilTabs = $DIC['ilTabs'];
        $lng = $DIC['lng'];
        $ilHelp = $DIC['ilHelp'];

        $ilHelp->setScreenIdComponent("dcl");

        // list records
        if ($ilAccess->checkAccess('read', "", $this->object->getRefId())) {
            $ilTabs->addTab("id_records", $lng->txt("content"),
                $this->ctrl->getLinkTargetByClass("ildclrecordlistgui", "show"));
        }

        // info screen
        if ($ilAccess->checkAccess('visible', "", $this->object->getRefId()) || $ilAccess->checkAccess('read', "",
                $this->object->getRefId())) {
            $ilTabs->addTab("id_info", $lng->txt("info_short"),
                $this->ctrl->getLinkTargetByClass("ilinfoscreengui", "showSummary"));
        }

        // settings
        if ($ilAccess->checkAccess('write', "", $this->object->getRefId())) {
            $ilTabs->addTab("id_settings", $lng->txt("settings"), $this->ctrl->getLinkTarget($this, "editObject"));
        }

        // list tables
        if ($ilAccess->checkAccess('write', "", $this->object->getRefId())) {
            $ilTabs->addTab("id_tables", $lng->txt("dcl_tables"),
                $this->ctrl->getLinkTargetByClass("ildcltablelistgui", "listTables"));
        }

        // export
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId())) {
            $ilTabs->addTab("export", $lng->txt("export"), $this->ctrl->getLinkTargetByClass("ildclexportgui", ""));
        }

        // edit permissions
        if ($ilAccess->checkAccess('edit_permission', "", $this->object->getRefId())) {
            $ilTabs->addTab("id_permissions", $lng->txt("perm_settings"),
                $this->ctrl->getLinkTargetByClass("ilpermissiongui", "perm"));
        }
    }

    /**
     * edit object
     * @access    public
     */
    public function editObject() : void
    {
        $tpl = $this->tpl;
        $ilTabs = $this->tabs_gui;
        $ilErr = $this->error;

        if (!$this->checkPermissionBool("write")) {
            $ilErr->raiseError($this->lng->txt("msg_no_perm_write"), $ilErr->MESSAGE);
        }

        $ilTabs->activateTab("settings");

        $form = $this->initEditForm();
        $values = $this->getEditFormValues();
        if ($values) {
            $form->setValuesByArray($values, true);
        }

        $this->addExternalEditFormCustom($form);

        $tpl->setContent($form->getHTML());
    }

    protected function initEditForm() : ilPropertyFormGUI
    {
        $this->tabs_gui->activateTab("id_settings");
        $this->lng->loadLanguageModule($this->object->getType());

        $form = new ilPropertyFormGUI();
        $form->setFormAction($this->ctrl->getFormAction($this, "update"));
        $form->setTitle($this->lng->txt($this->object->getType() . "_edit"));

        // title
        $ti = new ilTextInputGUI($this->lng->txt("title"), "title");
        $ti->setSize(min(40, ilObject::TITLE_LENGTH));
        $ti->setMaxLength(ilObject::TITLE_LENGTH);
        $ti->setRequired(true);
        $form->addItem($ti);

        // description
        $ta = new ilTextAreaInputGUI($this->lng->txt("description"), "desc");
        $ta->setCols(40);
        $ta->setRows(2);
        $form->addItem($ta);

        // is_online
        $cb = new ilCheckboxInputGUI($this->lng->txt("online"), "is_online");
        $cb->setInfo($this->lng->txt("dcl_online_info"));
        $form->addItem($cb);

        // Notification
        $cb = new ilCheckboxInputGUI($this->lng->txt("dcl_activate_notification"), "notification");
        $cb->setInfo($this->lng->txt("dcl_notification_info"));
        $form->addItem($cb);

        //table order
        $order_options = array();
        foreach ($this->getDataCollectionObject()->getTables() as $table) {
            $order_options[$table->getId()] = $table->getTitle();
        }

        // tile img upload
        $section_appearance = new ilFormSectionHeaderGUI();
        $section_appearance->setTitle($this->lng->txt('cont_presentation'));
        $form->addItem($section_appearance);
        $form = $this->object_service->commonSettings()->legacyForm($form, $this->object)->addTileImage();

        $form->addCommandButton("update", $this->lng->txt("save"));

        return $form;
    }

    /**
     * called by goto
     */
    public function listRecords()
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $ilCtrl->setParameterByClass("ildclrecordlistgui", "tableview_id", $_GET["tableview_id"]);
        $ilCtrl->redirectByClass("ildclrecordlistgui", "show");
    }

    /**
     * @return ilObjDataCollection
     */
    public function getDataCollectionObject()
    {
        $obj = new ilObjDataCollection($this->ref_id, true);

        return $obj;
    }

    /**
     * @param array $a_values
     * @return array|void
     */
    public function getEditFormCustomValues(array &$values) : void
    {
        $values["is_online"] = $this->object->getOnline();
        $values["rating"] = $this->object->getRating();
        $values["public_notes"] = $this->object->getPublicNotes();
        $values["approval"] = $this->object->getApproval();
        $values["notification"] = $this->object->getNotification();
    }

    public function updateCustom(ilPropertyFormGUI $orm) : void
    {
        $this->object->setOnline($orm->getInput("is_online"));
        $this->object->setRating($orm->getInput("rating"));
        $this->object->setPublicNotes($orm->getInput("public_notes"));
        $this->object->setApproval($orm->getInput("approval"));
        $this->object->setNotification($orm->getInput("notification"));

        $this->object_service->commonSettings()->legacyForm($orm, $this->object)->saveTileImage();

        $this->emptyInfo();
    }

    private function emptyInfo()
    {
        global $DIC;
        $lng = $DIC['lng'];
        $this->table = ilDclCache::getTableCache($this->object->getFirstVisibleTableId());
        $tables = $this->object->getTables();
        if (count($tables) == 1 and count($this->table->getRecordFields()) == 0 and count($this->table->getRecords()) == 0
            and $this->object->getOnline()
        ) {
            $this->tpl->setOnScreenMessage('info', $lng->txt("dcl_no_content_warning"), true);
        }
    }

    public function toggleNotification()
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $ilUser = $DIC['ilUser'];

        switch ($_GET["ntf"]) {
            case 1:
                ilNotification::setNotification(ilNotification::TYPE_DATA_COLLECTION, $ilUser->getId(), $this->obj_id,
                    false);
                break;
            case 2:
                ilNotification::setNotification(ilNotification::TYPE_DATA_COLLECTION, $ilUser->getId(), $this->obj_id,
                    true);
                break;
        }
        $ilCtrl->redirectByClass("ildclrecordlistgui", "show");
    }

    public function addHeaderAction() : void
    {
        global $DIC;
        $ilUser = $DIC['ilUser'];
        $ilAccess = $DIC['ilAccess'];
        $tpl = $DIC['tpl'];
        $lng = $DIC['lng'];
        $ilCtrl = $DIC['ilCtrl'];

        $dispatcher = new ilCommonActionDispatcherGUI(ilCommonActionDispatcherGUI::TYPE_REPOSITORY, $ilAccess, "dcl",
            $this->ref_id, $this->obj_id);

        ilObjectListGUI::prepareJSLinks(
            $this->ctrl->getLinkTarget($this, "redrawHeaderAction", "", true),
            $ilCtrl->getLinkTargetByClass(
                array(
                    "ilcommonactiondispatchergui",
                    "ilnotegui",
                ),
                "",
                "",
                true,
                false
            ),
            $ilCtrl->getLinkTargetByClass(array("ilcommonactiondispatchergui", "iltagginggui"), "", "", true, false)
        );

        $lg = $dispatcher->initHeaderAction();
        //$lg->enableNotes(true);
        //$lg->enableComments(ilObjWiki::_lookupPublicNotes($this->getPageObject()->getParentId()), false);

        // notification
        if ($ilUser->getId() != ANONYMOUS_USER_ID and $this->object->getNotification() == 1) {
            if (ilNotification::hasNotification(ilNotification::TYPE_DATA_COLLECTION, $ilUser->getId(),
                $this->obj_id)) {
                //Command Activate Notification
                $ilCtrl->setParameter($this, "ntf", 1);
                $lg->addCustomCommand($ilCtrl->getLinkTarget($this, "toggleNotification"),
                    "dcl_notification_deactivate_dcl");

                $lg->addHeaderIcon("not_icon", ilUtil::getImagePath("notification_on.svg"),
                    $lng->txt("dcl_notification_activated"));
            } else {
                //Command Deactivate Notification
                $ilCtrl->setParameter($this, "ntf", 2);
                $lg->addCustomCommand($ilCtrl->getLinkTarget($this, "toggleNotification"),
                    "dcl_notification_activate_dcl");

                $lg->addHeaderIcon("not_icon", ilUtil::getImagePath("notification_off.svg"),
                    $lng->txt("dcl_notification_deactivated"));
            }
            $ilCtrl->setParameter($this, "ntf", "");
        }

        $tpl->setHeaderActionMenu($lg->getHeaderAction());
    }
}
