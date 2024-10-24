<?php declare(strict_types=0);
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * TableGUI class for learning progress
 * @author       Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @version      $Id$
 * @ilCtrl_Calls ilLPObjectStatisticsDailyTableGUI: ilFormPropertyDispatchGUI
 * @ingroup      ServicesTracking
 */
class ilLPObjectStatisticsDailyTableGUI extends ilLPTableBaseGUI
{
    protected ?array $preselected;

    /**
     * Constructor
     */
    public function __construct(
        ?object $a_parent_obj,
        string $a_parent_cmd,
        array $a_preselect = null,
        bool $a_load_items = true
    ) {
        $this->preselected = $a_preselect;

        $this->setId("lpobjstatdlytbl");
        parent::__construct($a_parent_obj, $a_parent_cmd);

        $this->setShowRowsSelector(true);
        // $this->setLimit(ilSearchSettings::getInstance()->getMaxHits());
        $this->initFilter();

        $this->addColumn("", "", "1", true);
        $this->addColumn($this->lng->txt("trac_title"), "title");
        $this->addColumn($this->lng->txt("object_id"), "obj_id");
        for ($loop = 0; $loop < 24; $loop += 2) {
            $this->addColumn(str_pad($loop, 2, "0", STR_PAD_LEFT) . ":00-<br />" .
                str_pad((string) $loop, 2, "0", STR_PAD_LEFT) . ":00 ", "hour" . $loop);
        }
        $this->addColumn($this->lng->txt("total"), "sum");

        $this->setTitle($this->lng->txt("trac_object_stat_daily"));

        // $this->setSelectAllCheckbox("item_id");
        $this->addMultiCommand("showDailyGraph", $this->lng->txt("trac_show_graph"));
        $this->setResetCommand("resetDailyFilter");
        $this->setFilterCommand("applyDailyFilter");

        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj, $a_parent_cmd));
        $this->setRowTemplate("tpl.lp_object_statistics_daily_row.html", "Services/Tracking");
        $this->setEnableHeader(true);
        $this->setEnableNumInfo(true);
        $this->setEnableTitle(true);
        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");

        $this->setExportFormats(array(self::EXPORT_EXCEL, self::EXPORT_CSV));

        if ($a_load_items) {
            $this->getItems();
        }
    }

    public function numericOrdering(string $a_field) : bool
    {
        if ($a_field != "title") {
            return true;
        }
        return false;
    }

    /**
     * Init filter
     */
    public function initFilter() : void
    {
        $this->setDisableFilterHiding(true);

        // object type selection
        $si = new ilSelectInputGUI($this->lng->txt("obj_type"), "type");
        $si->setOptions($this->getPossibleTypes(true, false, true));
        $this->addFilterItem($si);
        $si->readFromSession();
        if (!$si->getValue()) {
            $si->setValue("crs");
        }
        $this->filter["type"] = $si->getValue();

        // title/description
        $ti = new ilTextInputGUI($this->lng->txt("trac_title_description"), "query");
        $ti->setMaxLength(64);
        $ti->setSize(20);
        $this->addFilterItem($ti);
        $ti->readFromSession();
        $this->filter["query"] = $ti->getValue();

        // read_count/spent_seconds
        $si = new ilSelectInputGUI($this->lng->txt("trac_figure"), "figure");
        $si->setOptions(array("read_count" => $this->lng->txt("trac_read_count"),
                              "spent_seconds" => $this->lng->txt("trac_spent_seconds")
        ));
        $this->addFilterItem($si);
        $si->readFromSession();
        if (!$si->getValue()) {
            $si->setValue("read_count");
        }
        $this->filter["measure"] = $si->getValue();

        // year/month
        $si = new ilSelectInputGUI($this->lng->txt("year") . " / " . $this->lng->txt("month"), "yearmonth");
        $si->setOptions($this->getMonthsFilter());
        $this->addFilterItem($si);
        $si->readFromSession();
        if (!$si->getValue()) {
            $si->setValue(date("Y-m"));
        }
        $this->filter["yearmonth"] = $si->getValue();
    }

    public function getItems() : void
    {
        $data = array();

        $objects = [];
        if ($this->filter["type"] != "prtf") {
            // JF, 2016-06-06
            $objects = $this->searchObjects($this->getCurrentFilter(true), "", null, false);

            if ($this->filter["type"] == "blog") {
                foreach (ilTrQuery::getWorkspaceBlogs($this->filter["query"]) as $obj_id) {
                    $objects[$obj_id] = array($obj_id);
                }
            }
        } else {
            // portfolios are not part of repository
            foreach (ilTrQuery::getPortfolios($this->filter["query"]) as $obj_id) {
                $objects[$obj_id] = array($obj_id);
            }
        }

        if ($objects) {

            $yearmonth = explode("-", $this->filter["yearmonth"]);
            if (sizeof($yearmonth) == 1) {
                $stat_objects = ilTrQuery::getObjectDailyStatistics($objects, $yearmonth[0]);
            } else {
                $stat_objects = ilTrQuery::getObjectDailyStatistics($objects, (string) $yearmonth[0],
                    (string) $yearmonth[1]);
            }

            foreach ($stat_objects as $obj_id => $hours) {
                $data[$obj_id]["obj_id"] = $obj_id;
                $data[$obj_id]["title"] = ilObject::_lookupTitle($obj_id);

                foreach ($hours as $hour => $values) {
                    // table data
                    $data[$obj_id]["hour" . floor($hour / 2) * 2] += (int) $values[$this->filter["measure"]];
                    $data[$obj_id]["sum"] += (int) $values[$this->filter["measure"]];

                    // graph data
                    $data[$obj_id]["graph"]["hour" . $hour] = $values[$this->filter["measure"]];
                }
            }

            // add objects with no usage data
            foreach ($objects as $obj_id => $ref_ids) {
                if (!isset($data[$obj_id])) {
                    $data[$obj_id]["obj_id"] = $obj_id;
                    $data[$obj_id]["title"] = ilObject::_lookupTitle($obj_id);
                }
            }
        }

        $this->setData($data);
    }

    /**
     * Fill table row
     */
    protected function fillRow(array $a_set) : void
    {
        $type = ilObject::_lookupType($a_set["obj_id"]);
        $this->tpl->setVariable("OBJ_ID", $a_set["obj_id"]);
        $this->tpl->setVariable("ICON_SRC", ilObject::_getIcon(0, "tiny", $type));
        $this->tpl->setVariable("ICON_ALT", $this->lng->txt($type));
        $this->tpl->setVariable("TITLE_TEXT", $a_set["title"]);

        if ($this->preselected && in_array($a_set["obj_id"], $this->preselected)) {
            $this->tpl->setVariable("CHECKBOX_STATE", " checked=\"checked\"");
        }

        $this->tpl->setCurrentBlock("hour");
        for ($loop = 0; $loop < 24; $loop += 2) {
            $value = (int) $a_set["hour" . $loop];
            if ($this->filter["measure"] != "spent_seconds") {
                $value = $this->anonymizeValue($value);
            } else {
                $value = $this->formatSeconds($value, true);
            }
            $this->tpl->setVariable("HOUR_VALUE", $value);
            $this->tpl->parseCurrentBlock();
        }

        if ($this->filter["measure"] == "spent_seconds") {
            $sum = $this->formatSeconds((int) $a_set["sum"], true);
        } else {
            $sum = $this->anonymizeValue((int) $a_set["sum"]);
        }
        $this->tpl->setVariable("TOTAL", $sum);
    }

    public function getGraph(array $a_graph_items) : string
    {
        global $DIC;

        $lng = $DIC['lng'];

        $chart = ilChart::getInstanceByType(ilChart::TYPE_GRID, "objstdly");
        $chart->setSize(700, 500);

        $legend = new ilChartLegend();
        $chart->setLegend($legend);

        $max_value = 0;
        foreach ($this->getData() as $object) {
            if (in_array($object["obj_id"], $a_graph_items)) {
                $series = $chart->getDataInstance(ilChartGrid::DATA_LINES);
                $series->setLabel(ilObject::_lookupTitle($object["obj_id"]));

                for ($loop = 0; $loop < 24; $loop++) {
                    $value = (int) $object["graph"]["hour" . $loop];
                    $max_value = max($max_value, $value);
                    if ($this->filter["measure"] != "spent_seconds") {
                        $value = $this->anonymizeValue($value, true);
                    }
                    $series->addPoint($loop, $value);
                }

                $chart->addData($series);
            }
        }

        $value_ticks = $this->buildValueScale(
            $max_value,
            ($this->filter["measure"] != "spent_seconds"),
            ($this->filter["measure"] == "spent_seconds")
        );

        $labels = array();
        for ($loop = 0; $loop < 24; $loop++) {
            $labels[$loop] = str_pad((string) $loop, 2, "0", STR_PAD_LEFT);
        }
        $chart->setTicks($labels, $value_ticks, true);

        return $chart->getHTML();
    }

    protected function fillMetaExcel(ilExcel $a_excel, int &$a_row) : void
    {
    }

    protected function fillRowExcel(ilExcel $a_excel, int &$a_row, array $a_set) : void
    {
        $a_excel->setCell($a_row, 0, ilObject::_lookupTitle($a_set["obj_id"]));
        $a_excel->setCell($a_row, 1, $a_set["obj_id"]);

        $col = 1;
        for ($loop = 0; $loop < 24; $loop += 2) {
            $value = (int) $a_set["hour" . $loop];
            if ($this->filter["measure"] != "spent_seconds") {
                $value = $this->anonymizeValue($value);
            }

            $a_excel->setCell($a_row, ++$col, $value);
        }

        if ($this->filter["measure"] == "spent_seconds") {
            // keep seconds
            // $sum = $this->formatSeconds((int)$a_set["sum"]);
            $sum = (int) $a_set["sum"];
        } else {
            $sum = $this->anonymizeValue((int) $a_set["sum"]);
        }
        $a_excel->setCell($a_row, ++$col, $sum);
    }

    protected function fillMetaCSV(ilCSVWriter $a_csv) : void
    {
    }

    protected function fillRowCSV(ilCSVWriter $a_csv, array $a_set) : void
    {
        $a_csv->addColumn(ilObject::_lookupTitle($a_set["obj_id"]));
        $a_csv->addColumn($a_set["obj_id"]);

        for ($loop = 0; $loop < 24; $loop += 2) {
            $value = (int) $a_set["hour" . $loop];
            if ($this->filter["measure"] != "spent_seconds") {
                $value = $this->anonymizeValue($value);
            }

            $a_csv->addColumn($value);
        }

        if ($this->filter["measure"] == "spent_seconds") {
            // keep seconds
            // $sum = $this->formatSeconds((int)$a_set["sum"]);
            $sum = (int) $a_set["sum"];
        } else {
            $sum = $this->anonymizeValue((int) $a_set["sum"]);
        }
        $a_csv->addColumn($sum);

        $a_csv->addRow();
    }
}
