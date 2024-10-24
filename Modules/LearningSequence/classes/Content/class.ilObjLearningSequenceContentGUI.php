<?php declare(strict_types=1);

/* Copyright (c) 2021 - Daniel Weise <daniel.weise@concepts-and-training.de> - Extended GPL, see LICENSE */
/* Copyright (c) 2021 - Nils Haagen <nils.haagen@concepts-and-training.de> - Extended GPL, see LICENSE */

use ILIAS\HTTP\Wrapper\ArrayBasedRequestWrapper;
use ILIAS\Refinery\Factory;

class ilObjLearningSequenceContentGUI
{
    const CMD_MANAGE_CONTENT = "manageContent";
    const CMD_SAVE = "save";
    const CMD_DELETE = "delete";
    const CMD_CONFIRM_DELETE = "confirmDelete";
    const CMD_CANCEL = "cancel";

    const FIELD_ORDER = 'f_order';
    const FIELD_ONLINE = 'f_online';
    const FIELD_POSTCONDITION_TYPE = 'f_pct';

    protected ilObjLearningSequenceGUI $parent_gui;
    protected ilCtrl $ctrl;
    protected ilGlobalTemplateInterface $tpl;
    protected ilLanguage $lng;
    protected ilAccess $access;
    protected ilConfirmationGUI $confirmation_gui;
    protected LSItemOnlineStatus $ls_item_online_status;
    protected ArrayBasedRequestWrapper $post_wrapper;
    protected ILIAS\Refinery\Factory $refinery;

    public function __construct(
        ilObjLearningSequenceGUI $parent_gui,
        ilCtrl $ctrl,
        ilGlobalTemplateInterface $tpl,
        ilLanguage $lng,
        ilAccess $access,
        ilConfirmationGUI $confirmation_gui,
        LSItemOnlineStatus $ls_item_online_status,
        ArrayBasedRequestWrapper $post_wrapper,
        Factory $refinery
    ) {
        $this->parent_gui = $parent_gui;
        $this->ctrl = $ctrl;
        $this->tpl = $tpl;
        $this->lng = $lng;
        $this->access = $access;
        $this->confirmation_gui = $confirmation_gui;
        $this->ls_item_online_status = $ls_item_online_status;
        $this->post_wrapper = $post_wrapper;
        $this->refinery = $refinery;
    }

    public function executeCommand()
    {
        $cmd = $this->ctrl->getCmd();

        switch ($cmd) {
            case self::CMD_CANCEL:
                $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENT);
                break;
            case self::CMD_MANAGE_CONTENT:
            case self::CMD_SAVE:
            case self::CMD_DELETE:
            case self::CMD_CONFIRM_DELETE:
                $this->$cmd();
                break;
            default:
                throw new ilException("ilObjLearningSequenceContentGUI: Command not supported: $cmd");
        }
    }

    protected function manageContent() : void
    {
        // Adds a btn to the gui which allows adding possible objects.
        $this->parent_gui->showPossibleSubObjects();

        $data = $this->parent_gui->getObject()->getLSItems();
        $this->renderTable($data);
    }

    protected function renderTable(array $ls_items) : void
    {
        $table = new ilObjLearningSequenceContentTableGUI(
            $this,
            $this->parent_gui,
            self::CMD_MANAGE_CONTENT,
            $this->ctrl,
            $this->lng,
            $this->access,
            new ilAdvancedSelectionListGUI(),
            $this->ls_item_online_status
        );

        $table->setData($ls_items);
        $table->addMultiCommand(self::CMD_CONFIRM_DELETE, $this->lng->txt("delete"));

        $table->addCommandButton(self::CMD_SAVE, $this->lng->txt("save"));

        $this->tpl->setContent($table->getHtml());
    }

    /**
     * Handle the confirmDelete command
     */
    protected function confirmDelete() : void
    {
        $ref_ids = $this->post_wrapper->retrieve(
            "id",
            $this->refinery->kindlyTo()->listOf($this->refinery->kindlyTo()->int())
        );

        if (!$ref_ids || count($ref_ids) < 1) {
            $this->tpl->setOnScreenMessage("info", $this->lng->txt('no_entries_selected_for_delete'), true);
            $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENT);
        }

        foreach ($ref_ids as $ref_id) {
            $obj = ilObjectFactory::getInstanceByRefId($ref_id);
            $this->confirmation_gui->addItem("id[]", $ref_id, $obj->getTitle());
        }

        $this->confirmation_gui->setFormAction($this->ctrl->getFormAction($this));
        $this->confirmation_gui->setHeaderText($this->lng->txt("delete_confirmation"));
        $this->confirmation_gui->setConfirm($this->lng->txt("confirm"), self::CMD_DELETE);
        $this->confirmation_gui->setCancel($this->lng->txt("cancel"), self::CMD_CANCEL);

        $this->tpl->setContent($this->confirmation_gui->getHTML());
    }

    protected function delete() : void
    {
        $ref_ids = $this->post_wrapper->retrieve(
            "id",
            $this->refinery->kindlyTo()->listOf($this->refinery->kindlyTo()->int())
        );

        $this->parent_gui->getObject()->deletePostConditionsForSubObjects($ref_ids);

        $this->tpl->setOnScreenMessage("success", $this->lng->txt('entries_deleted'), true);
        $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENT);
    }

    /**
     * @return array<"value" => "option_text">
     */
    public function getPossiblePostConditionsForType(string $type) : array
    {
        return $this->parent_gui->getObject()->getPossiblePostConditionsForType($type);
    }


    public function getFieldName(string $field_name, int $ref_id) : string
    {
        return implode('_', [$field_name, (string) $ref_id]);
    }

    protected function save() : void
    {
        $data = $this->parent_gui->getObject()->getLSItems();

        $updated = [];
        foreach ($data as $lsitem) {
            $ref_id = $lsitem->getRefId();
            $online = $this->getFieldName(self::FIELD_ONLINE, $ref_id);
            $order = $this->getFieldName(self::FIELD_ORDER, $ref_id);
            $condition_type = $this->getFieldName(self::FIELD_POSTCONDITION_TYPE, $ref_id);

            $condition_type = $this->post_wrapper->retrieve($condition_type, $this->refinery->kindlyTo()->string());
            $online = $this->post_wrapper->retrieve($online, $this->refinery->kindlyTo()->bool());
            $order = $this->post_wrapper->retrieve($order, $this->refinery->kindlyTo()->int());

            $condition = $lsitem->getPostCondition()
                ->withConditionOperator($condition_type);
            $updated[] = $lsitem
                ->withOnline($online)
                ->withOrderNumber($order)
                ->withPostCondition($condition);
        }

        $this->parent_gui->getObject()->storeLSItems($updated);
        $this->tpl->setOnScreenMessage("success", $this->lng->txt('entries_updated'), true);
        $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENT);
    }
}
