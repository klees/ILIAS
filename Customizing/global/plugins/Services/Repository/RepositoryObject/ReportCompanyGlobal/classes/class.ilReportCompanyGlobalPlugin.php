<?php

require_once 'Services/ReportsRepository/classes/class.ilReportBasePlugin.php';

class ilReportCompanyGlobalPlugin extends ilReportBasePlugin {
	// must correspond to the plugin subdirectory
	protected function getReportName() {
		return "ReportCompanyGlobal";
	}
}