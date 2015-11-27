<?php

require_once 'Services/Repository/classes/class.ilObjectPluginGUI.php';
require_once 'Services/Form/classes/class.ilPropertyFormGUI.php';
require_once 'Services/Form/classes/class.ilTextInputGUI.php';
require_once 'Services/Form/classes/class.ilTextAreaInputGUI.php';
require_once 'Services/CaTUIComponents/classes/class.catTitleGUI.php';
require_once("Services/CaTUIComponents/classes/class.catTableGUI.php");
require_once("Services/CaTUIComponents/classes/class.catHSpacerGUI.php");


abstract class ilObjReportBaseGUI extends ilObjectPluginGUI {

	protected $gLng;
	protected $gCtrl;
	protected $gTpl;
	protected $gUser;
	protected $gLog;
	protected $gAccess;
	protected $settings_form;

	protected function afterConstructor() {
		global $lng, $ilCtrl, $tpl, $ilUser, $ilLog, $ilAccess, $ilTabs;	
		$this->gLng = $lng;
		$this->gCtrl = $ilCtrl;
		$this->gTpl = $tpl;
		$this->gUser = $ilUser;
		$this->gLog = $ilLog;
		$this->gAccess = $ilAccess;
		$this->gTabs = $ilTabs;


		// TODO: this is crapy. The root cause of this problem is, that the
		// filter should no need to know about it's action. The _rendering_
		// of the filter needs to know about the action.
		if ($this->object !== null) {
			$this->setFilterAction();
		}

		$this->title = null;
	}

	public function setTabs() {
		if($this->gAccess->checkAccess("write", "", $this->object->getRefId())) {
			
			// tab for the "show content" command
			if ($this->gAccess->checkAccess("read", "", $this->object->getRefId())) {
				$this->gTabs->addTab("content", $this->txt("content"),
				$this->gCtrl->getLinkTarget($this, "showContent"));
			}

			// standard info screen tab
			$this->addInfoTab();

			// a "properties" tab
			$this->gTabs->addTab("properties", $this->txt("properties"),
			$this->gCtrl->getLinkTarget($this, "settings"));			

			// standard epermission tab
			$this->addPermissionTab();
		}
	}

	/**
	* Besides usual report commands (exportXLS, view, ...) showMenu goes here
	*/
	public function performCommand() {
		$cmd = $this->gCtrl->getCmd("showContent");

		switch ($cmd) {
			case "saveSettings":
				if($this->gAccess->checkAccess("write", "", $this->object->getRefId())) {
					$this->gTabs->activateTab("properties");
					return $this->saveSettings();
				}
				break;
			case "settings":
				if($this->gAccess->checkAccess("write", "", $this->object->getRefId())) {
					$this->gTabs->activateTab("properties");
					return $this->renderSettings();
				}
				break;
			case "exportxls":
				$this->exportXLS();
				exit();
			//no "break;" !
			case "showContent":
				if($this->gAccess->checkAccess("read", "", $this->object->getRefId())) {
					$this->gTabs->activateTab("content");
					return $this->renderReport();
				}
				break;
			default:
				throw new ilException("Unknown Command '$cmd'.");
		}
	}

	public function getAfterCreationCmd() {
		return "settings";
	}

	public function getStandardCmd() {
		return "showContent";
	}


	/**
	* render report.
	*/
	final public function renderReport() {
		$this->object->prepareReport();
		$this->title = $this->prepareTitle(catTitleGUI::create());
		$this->spacer = $this->prepareSpacer(new catHSpacerGUI());
		$this->table = $this->prepareTable(new catTableGUI($this, "showContent"));
		$this->gTpl->setContent($this->render());
	}
		
	protected function render() {
		$this->gTpl->setTitle(null);
		return 	($this->title !== null ? $this->title->render() : "")
				. ($this->object->deliverFilter() !== null ? $this->object->deliverFilter()->render() : "")
				. ($this->spacer !== null ? $this->spacer->render() : "")
				. $this->renderTable();
	}
	
	protected function renderTable() {
		$callback = get_class($this).'::transformResultRow';
		if ($this->object->deliverTable()->_group_by !== null) {
			$data = $this->object->deliverGroupedData($callback);
			$content = $this->renderGroupedTable($data);
		} else {
			$data = $this->object->deliverData($callback);
			$content = $this->renderUngroupedTable($data);
		}
		//export-button
		$export_btn = "";
		if (count($data) > 0) {
			$export_btn = $this->renderExportButton();
		}

		return	 $export_btn
				.$content
				.$export_btn;
	}
	
	protected function renderExportButton() {
		$this->enableRelevantParametersCtrl();
		$export_btn = '<a class="submit exportXlsBtn"'
						. 'href="'
						.$this->gCtrl->getLinkTarget($this, "exportxls")
						.'">'
						.$this->gLng->txt("gev_report_exportxls")
						.'</a>';
		$this->disableRelevantParametersCtrl();
		return $export_btn;
	}

	protected function prepareTable($a_table) {
		$a_table->setEnableTitle(false);
		$a_table->setTopCommands(false);
		$a_table->setEnableHeader(true);
		return $a_table;
	}

	protected function prepareTitle($a_title) {
		$a_title->title($this->object->getTitle())
				->subTitle($this->object->getDescription())
				->useLng(false);
		return $a_title;
	}

	protected function prepareSpacer($a_spacer) {
		return $a_spacer;
	}

	protected function renderUngroupedTable($data) {

		if(!$this->object->deliverTable()->row_template_filename) {
			throw new Exception("No template defined for table ".get_class($this));
		}
		$this->table->setRowTemplate(
			$this->object->deliverTable()->row_template_filename, 
			$this->object->deliverTable()->row_template_module
		);

		$this->table->addColumn("", "blank", "0px", false);
		foreach ($this->object->deliverTable()->columns as $col) {
			$this->table->addColumn( $col[2] ? $col[1] : $this->lng->txt($col[1])
							 , $col[0]
							 , $col[3]
							 );
		}
		
		if ($this->object->deliverOrder() !== null) {
			$this->table->setOrderField($this->object->deliverOrder()->getOrderField());
			$this->table->setOrderDirection($this->object->deliverOrder()->getOrderDirection());
		}
		
		$cnt = count($data);
		$this->table->setLimit($cnt);
		$this->table->setMaxCount($cnt);
		$external_sorting = true;

		if($this->object->deliverOrder() === null || 
			in_array($this->object->deliverOrder()->getOrderField(), 
				$this->internal_sorting_fields ? $this->internal_sorting_fields : array())
			) {
				$external_sorting = false;	
		}
		
		$this->table->setExternalSorting($external_sorting);
		if ($this->internal_sorting_numeric) {
			foreach ($this->internal_sorting_numeric as $col) {
				$table->numericOrdering($col);
			}
		}

		$this->table->setData($data);
		$this->enableRelevantParametersCtrl();
		$return = $this->table->getHtml();
		$this->disableRelevantParametersCtrl();
		return $return;
	}

	protected function renderGroupedTable($data) {
		$content = "";
		foreach ($data as $key => $rows) {
			// We know for sure there is at least one entry in the rows
			// since we created a group from it.
			$content .= $this->renderGroupHeader($rows[0]);
			$content .= $this->renderUngroupedTable($rows);
		}
		return $content;
	}

	protected function renderGroupHeader($data) {
		$tpl = new ilTemplate( $this->object->deliverTable()->group_head_template_filename
							 , true, true
							 , $this->object->deliverTable()->group_head_template_module
							 );

		foreach ($this->object->deliverTable()->_group_by as $key => $conf) {
			$tpl->setVariable("VAL_".strtoupper($key), $data[$key]);
			$tpl->setVariable("TITLE_".strtoupper($key)
							 , $conf[2] ? $conf[1] : $this->lng->txt($conf[1]));
		}
		return $tpl->get();
	}

	/**
	* provide xls version of report for download.
	*/
	protected function exportXLS() {
		require_once "Services/Excel/classes/class.ilExcelUtils.php";
		require_once "Services/Excel/classes/class.ilExcelWriterAdapter.php";
		$this->object->prepareReport();

		$adapter = new ilExcelWriterAdapter("Report.xls", true); 
		$workbook = $adapter->getWorkbook();
		$worksheet = $workbook->addWorksheet();
		$worksheet->setLandscape();

		//available formats within the sheet
		$format_bold = $workbook->addFormat(array("bold" => 1));
		$format_wrap = $workbook->addFormat();
		$format_wrap->setTextWrap();
		
		//init cols and write titles
		$colcount = 0;
		foreach ($this->object->deliverTable()->all_columns as $col) {
			if ($col[4]) {
				continue;
			}
			$worksheet->setColumn($colcount, $colcount, 30); //width
			if (method_exists($this, "_process_xls_header") && $col[2]) {
				$worksheet->writeString(0, $colcount, $this->_process_xls_header($col[1]), $format_bold);
			}
			else {
				$worksheet->writeString(0, $colcount, $col[2] ? $col[1] : $this->lng->txt($col[1]), $format_bold);
			}
			$colcount++;
		}

		//write data-rows
		$rowcount = 1;
		$callback = get_class($this).'::transformResultRowXLS';
		foreach ($this->object->deliverData($callback) as $entry) {
			$colcount = 0;
			foreach ($this->object->deliverTable()->all_columns as $col) {
				if ($col[4]) {
					continue;
				}
				$k = $col[0];
				$v = $entry[$k];

				$method_name = '_process_xls_' .$k;
				if (method_exists($this, $method_name)) {
					$v = $this->$method_name($v);
				}
				$worksheet->write($rowcount, $colcount, $v, $format_wrap);
				$colcount++;
			}
			$rowcount++;
		}
		$workbook->close();		
	}

	/**
	* to be used in ilObjReport<>::getData
	*/
	public static function transformResultRow($a_rec) {
		$a_rec = self::replaceEmpty($a_rec);
		return $a_rec;
	}

	public static function transformResultRowXLS($a_rec) {
		$a_rec = self::replaceEmpty($a_rec);
		return $a_rec;
	}

	final protected static function replaceEmpty($a_rec) {
		global $lng;
		foreach ($a_rec as $key => $value) {
			if ($a_rec[$key] == "-empty-" || $a_rec[$key] == "0000-00-00" || $a_rec[$key] === null) {
				$a_rec[$key] = $lng->txt("gev_table_no_entry");
			}
		}
		return $a_rec;
	}

	/**
	* housekeeping the get parameters passed to ctrl
	*/
	final protected function enableRelevantParametersCtrl() {
		foreach ($this->object->getRelevantParameters() as $get_parameter => $get_value) {
			$this->gCtrl->setParameter($this, $get_parameter, $get_value);
		}
	}

	final protected function disableRelevantParametersCtrl() {
		foreach ($this->object->getRelevantParameters() as $get_parameter => $get_value) {
			$this->gCtrl->setParameter($this, $get_parameter, null);
		}
	}

	protected function setFilterAction() {
		$this->enableRelevantParametersCtrl();
		$this->object->setFilterAction($this->gCtrl->getLinkTarget($this,'showContent'));
		$this->disableRelevantParametersCtrl();
	}


	/**
	* Settings menu of the report. Note that any setting query will be performed inside ilObjBaseReport.
	* Allways call parent methods in final plugin-classmethod static::settingFrom, static::getSettingsData and static::saveSettingsData.
	*/
	protected function renderSettings() {
		$data = $this->getSettingsData();
		$settings_form = $this->settingsForm($data);
		$this->gTpl->setContent($settings_form->getHtml());
	}

	protected function saveSettings() {
		$settings_form = $this->settingsForm();
		$settings_form->setValuesByPost();
		if($settings_form->checkInput()) {
			$this->saveSettingsData($_POST);
		}
		$this->gTpl->setContent($settings_form->getHtml());
	}

	protected function getSettingsData() {

		$data["title"] = $this->object->getTitle();
		$data["description"] = $this->object->getDescription();

		return $data;
	}

	/*
	* call this method last in static::saveSettingsData
	*/
	protected function saveSettingsData($data) {
		$this->object->setTitle($data["title"]);
		$this->object->setDescription($data["description"]);
		$this->object->doUpdate();
		$this->object->update();
	}

	/*
	* call this method first in static::settingsForm
	*/
	protected function settingsForm($data = null) {
		$settings_form = new ilPropertyFormGUI();
		$settings_form->setFormAction($this->gCtrl->getFormAction($this));
		$settings_form->addCommandButton("saveSettings", $this->gLng->txt("save"));

		$title = new ilTextInputGUI($this->gLng->txt('title'),'title');
		if(isset($data["title"])) {
			$title->setValue($data["title"]);
		}
		$title->setRequired(true);
		$settings_form->addItem($title);

		$description = new ilTextAreaInputGUI($this->gLng->txt('description'),'description');
		if(isset($data["description"])) {
			$description->setValue($data["description"]);
		}
		$settings_form->addItem($description);

		return $settings_form;
	}
}