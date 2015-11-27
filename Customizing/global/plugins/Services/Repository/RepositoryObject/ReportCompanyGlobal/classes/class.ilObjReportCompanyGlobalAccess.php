<?php

require_once 'Services/ReportsRepository/classes/class.ilObjReportBaseAccess.php';


class ilObjReportCompanyGlobalAccess extends ilObjReportBaseAccess {

	/**
	* {@inheritdoc}
	*/
	static public function checkOnline($a_id) {
		global $ilDB;

		$set = $ilDB->query("SELECT is_online FROM rep_robj_rbi ".
			" WHERE id = ".$ilDB->quote($a_id, "integer")
			);
		$rec  = $ilDB->fetchAssoc($set);
		return (boolean) $rec["is_online"];
	}

}