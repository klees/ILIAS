<?php

/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Filter admin table
 *
 * @author Alexander Killing <killing@leifos.de>
 */
class ilContainerFilterTableGUI extends ilTable2GUI
{
    protected ilContainerFilterService $container_filter_service;
    protected int $ref_id;

    public function __construct(
        ilContainerFilterAdminGUI $a_parent_obj,
        string $a_parent_cmd,
        ilContainerFilterService $container_filter_service,
        ilObjCategory $cat
    ) {
        global $DIC;

        $this->id = "t";
        $this->lng = $DIC->language();
        $this->ctrl = $DIC->ctrl();
        $this->container_filter_service = $container_filter_service;
        $this->ref_id = $cat->getRefId();

        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->setData($this->getItems());
        $this->setTitle($this->lng->txt(""));

        $this->addColumn($this->lng->txt("cont_filter_record"));
        $this->addColumn($this->lng->txt("cont_filter_field"));
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
        $this->setRowTemplate("tpl.cont_filter_row.html", "Services/Container/Filter");
    }

    protected function getItems() : array
    {
        $service = $this->container_filter_service;

        $items = array_map(function ($i) use ($service) {
            /** @var ilContainerFilterField $i */
            return array(
                "record_set_id" => $i->getRecordSetId(),
                "record_title" => $service->util()->getContainerRecordTitle($i->getRecordSetId()),
                "field_title" => $service->util()->getContainerFieldTitle($i->getRecordSetId(), $i->getFieldId())
            );
        }, $service->data()->getFilterSetForRefId($this->ref_id)->getFields());
        return $items;
    }

    protected function fillRow(array $a_set) : void
    {
        $tpl = $this->tpl;

        $tpl->setVariable("RECORD_TITLE", $a_set["record_title"]);
        $tpl->setVariable("FIELD_TITLE", $a_set["field_title"]);
    }
}
