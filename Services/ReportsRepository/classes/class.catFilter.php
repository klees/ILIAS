<?php
/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */#

/**
* Base class for filters in reports:
*
* Does the following things:
*   - lets report creator define filters in a declarative way
*   - renders html output from filters
*   - creates strings to be appended as GET parameters to links in report table headers
*   - creates where-parts for sql queries based on filter declaration
*   - grabs current filter configuration from GET and POST 
*
* @author	Richard Klees <richard.klees@concepts-and-training.de>
* @version	$Id$
*/

// This class needs to be implemented to create new types of filters.
// Look at catDatePeriodFilterType to see how it is used.
abstract class catFilterType {
	// Get the ID of the filter type. (e.g. dateperiod)
	abstract public function getId();
	
	// Check the config the user made for the filter.
	// e.g. The user can call catFilter::dateperiod($a_name, ...)
	// where ... are some more params the filter might need. catFilter
	// creates an array of the form ($type_id, $name, ...), which is
	// handed to checkConfig.
	abstract public function checkConfig($a_conf);
	
	// Render the filter to HTML. $a_tpl is the tpl used for the complete
	// filter, where the appropriate block for the type is already set.
	// Rationale here is, that it might be desirable to render filters for
	// the reports in a different way than ILIAS-standard controls. Furthermore
	// a lot of options of the standard controls are not needed for the filters. 
	// $a_conf is the error previously passed to checkConfig.
	// $a_pars is the value(s) the filter currently is set to.
	abstract public function render($a_tpl, $a_conf, $a_pars);
	
	// Create a part of an sql-query to be appended to WHERE. 
	abstract public function sql($a_conf, $a_pars);
	
	// Preprocess the internally used parameter representation
	// before handing it to the outside world. e.g. "2014-10-10" => ilDate
	abstract public function get($a_pars);
	
	// Get the default for the filter type, based on configuration.
	abstract public function _default($a_conf);
	
	// Preprocess variables retreived from post before making them
	// a parameter for the filter. (e.g. checkbox => boolean)
	abstract public function preprocess_post($a_post);
}


// Represents a complete set of filters. New filters can be 
// appended via catFilter::$filter_type($a_name, ...)
class catFilter {
	// This stores the known types of filters...
	protected static $filter_types = array();
	
	// ... and this registers a new one.
	public static function addFilterType($a_id, $a_impl) {
		catFilter::$filter_types[$a_id] = $a_impl;
	}
	
	// Conditions that should be statically appended to the
	// query.
	protected $static_conditions;
	// The configurations of the filters (like $name => $conf)
	protected $filters;
	// The parameters of the filters (like $name => $param(s))
	protected $parameters;
	// The name of the filter parameter in the GET-params
	protected $get_string;
	// Are the filters compiled or not?
	protected $compiled;
	// The action to be used for click on [Filter]-button.
	protected $action;
	// The title to be used for said action.
	protected $action_title;
	// A prefix to be used for all filter related POST-vars.
	protected $post_var_prefix;
	// The string to be appended to GET to keep the parameters
	protected $encoded_params;
	
	protected function __construct() {
		global $lng;
		global $ilDB;
		
		$this->lng = &$lng;
		$this->db = &$ilDB;
		
		$this->static_conditions = array();
		$this->filters = array();
		$this->parameters = array();
		$this->get_string = null;
		$this->compiled = false;
		$this->template = null;
		$this->action = null;
		$this->action_title = null;
		$this->post_var_prefix = "filter";
		
		$this->encoded_params = null;
		$this->calendar_util_inited = false;
	}
	
	// Just a wrapper around the constructor to enable
	// creation of filters in fluid syntax.
	static public function create() {
		return new catFilter();
	}

	// magic for easy filter creation
	public function __call($a_name, $a_args) {
		if (array_key_exists($a_name, catFilter::$filter_types)) {
			$this->checkNotCompiled();
			$this->checkNameDoesNotExist($a_name);
			
			// I think this is necessary to have indexes from 0 to n
			$conf = array($a_name);
			foreach($a_args as $value) {
				$conf[] = $value;
			}
			
			$type = catFilter::$filter_types[$a_name];
			$this->filters[$a_args[0]] = $type->checkConfig($conf);
			return $this;
		}

		throw new Exception(" Call to undefined method catFilter::".$a_name);
	}

	// different types of filters
	
	// add a static condition to where
	public function static_condition($a_where) {
		$this->static_conditions[] = $a_where;
		return $this;
	}
	
	// set action to be called on submit
	public function action($a_action, $a_title = null) {
		$this->checkNotCompiled();
		if ($this->action !== null) {
			throw new Exception("catFilter::action: action is already set.");
		}
		
		if ($a_title === null) {
			$a_title = $this->lng->txt("gev_filter");
		}
		
		$this->action = $a_action;
		$this->action_title = $a_title;
		return $this;
	}

	// Compile the filter, no changes allowed afterwards.
	//
	// Initalizes parameters of filter from POST with fallback to GET and
	// default.
	public function compile() {
		if ($this->action === null) {
			throw new Exception("catFilter::compile: no action set.");
		}
		
		$this->compiled = true;
	
		$this->loadSearchParameters();
		
		return $this;
	}

	// Load search parameters, first try post, then try get, then use default
	protected function loadSearchParameters() {
		$get_params = array();
		if (array_key_exists($this->getGETName(), $_GET)) {
			$get_params = unserialize(base64_decode($_GET[$this->getGETName()]));
		}
		
		foreach($this->filters as $filter) {
			$name = $this->getName($filter);
			$postvar = $this->getPostVar($filter);
			if (array_key_exists($postvar, $_POST)) {
				$this->parameters[$name] = $this->preprocess_post($filter, $_POST[$postvar]);
			}
			else if(array_key_exists($name, $get_params)) {
				$this->parameters[$name] = $get_params[$name];
			}
			else {
				$this->parameters[$name] = $this->_default($filter);
			}
		}
	}

	// Get search parameters as string for GET.
	public function encodeSearchParamsForGET() {
		if ($this->encoded_params === null) {
			$this->encoded_params = base64_encode(serialize($this->parameters));
		}
		
		return $this->encoded_params;
	}
	
	// Get the name to be used to append parameters to get
	public function getGETName() {
		return "filter_params";
	}
	
	// Get the filters and current parameters as SQL-query part
	// to be append after WHERE in SQL-statement.
	public function getSQLWhere() {
		$stmt = " TRUE ";

		foreach ($this->static_conditions as $cond) {
			$stmt .= "\n AND " . $cond;
		}

		foreach ($this->filters as $conf) {
			if (!$this->isInWhere($conf)) {
				continue;
			}
			$type = $this->getType($conf);
			$pars = $this->getParameters($conf);
			$stmt .= "\n   AND " . $type->sql($conf, $pars) . " ";
		}
		
		return $stmt;
	}
	
	public function getSQLHaving() {
		$stmt = "";
		
		foreach ($this->filters as $conf) {
			if ($this->isInWhere($conf)) {
				continue;
			}
			$type = $this->getType($conf);
			$pars = $this->getParameters($conf);
			$stmt .= "\n   AND " . $type->sql($conf, $pars) . " ";
		}
		
		if ($stmt === "") {
			return "";
		}
		
		return " TRUE ".$stmt;
	}
	
	// Get the value of a filter parameter
	public function get($a_name) {
		if (!array_key_exists($a_name, $this->filters)) {
			throw new Exception("catFilter::get: Unknown filter ".$a_name);
		}
		
		$filter = $this->filters[$a_name];
		$type = $this->getType($filter);
		return $type->get($this->getParameters($filter));
	}
	
	// Render the filter to HTML output
	public function render() {
		$this->checkCompiled("render");

		if (count($this->filters) === 0) {
			return "";
		}

		require_once("Services/UICore/classes/class.ilTemplate.php");
		
		$out = "";
		
		$tpl = new ilTemplate("tpl.cat_filter.html", true, true, "Services/ReportsRepository");
		
		foreach ($this->filters as $conf) {
			$postvar = $this->getPostVar($conf);
			$type = $this->getType($conf);
			$type_id = $type->getId();
			
			$_tpl = new ilTemplate( "tpl.cat_filter_".$type_id.".html", true, true, "Services/ReportsRepository"
								  , array("POST_VAR" => $postvar));
			if($type->render($_tpl, $postvar, $conf, $this->getParameters($conf))) {
				$tpl->setCurrentBlock($type_id);
				$_tpl->setVariable("POST_VAR", $postvar);
				$tpl->setVariable("FILTER_ITEM", $_tpl->get());
				$tpl->setVariable("CSSID", $this->getName($conf));
				$tpl->parseCurrentBlock();
			}
		}
		
		$tpl->setVariable("POST_VAR_PREFIX", $this->post_var_prefix);
		
		$tpl->setVariable("ACTION", $this->action);
		$tpl->setVariable("FILTER", $this->action_title);
		
		return $tpl->get();
	}
	
	// get default value for filter
	protected function _default($a_conf) {
		$type = $this->getType($a_conf);
		return $type->_default($a_conf);
	}
	
	// preprocess post variables
	protected function preprocess_post($a_conf, $a_post) {
		$type = $this->getType($a_conf);
		return $type->preprocess_post($a_post);
	}
	
	protected function getPostVar($a_conf) {
		return $this->post_var_prefix . "_" . $this->getName($a_conf);
	}
	
	protected function getType($a_conf) {
		return catFilter::$filter_types[$a_conf[0]];
	}
	
	protected function isInWhere($a_conf) {
		return catFilter::$filter_types[$a_conf[0]]->isInWhere($a_conf);
	}

	protected function getName($a_conf) {
		return $a_conf[1];
	}
	
	protected function getDefault($a_conf) {
		return $a_conf[4];
	}
	
	protected function getParameters($a_conf) {
		return $this->parameters[$this->getName($a_conf)];
	}
	
	static public function quoteDBId($a_id) {
		global $ilDB;
		$spl = explode(".", $a_id);
		$ret = array();
		foreach ($spl as $id) {
			$ret[] = $ilDB->quoteIdentifier($id);
		}
		return implode(".", $ret);
	}
	
	// Methods for sanity checks.
	protected function checkNotCompiled() {
		if ($this->compiled) {
			throw new Exception("catFilter::checkCompiled: Don't modify a filter you already compiled.");
		}
	}

	protected function checkCompiled($a_what) {
		if (!$this->compiled) {
			throw new Exception("catFilter::checkCompiled: Don't ".$a_what." a filter you did not compile.");
		}
	}


	protected function checkNameDoesNotExist($a_name) {
		if (array_key_exists($a_name, $this->filters)) {
			throw new Exception("catFilter::checkNameExists: Name ".$a_name." already used.");
		}
	}



	static function getDistinctValues($a_field, $a_table, $a_order='ASC', $a_showempty=false, $a_filter_historic=false) {
		global $ilDB;
		$where = "WHERE TRIM($a_field) NOT IN ('-empty-', '')"
				." AND $a_field IS NOT NULL"
				;
		if ($a_showempty === true) {
			$where = 'WHERE 1';
		}
		if ($a_filter_historic === true) {
			$where .= ' AND hsit_historic=0';
		}


		$sql = "SELECT DISTINCT $a_field FROM $a_table $where ORDER BY $a_field $a_order";
		$res = $ilDB->query($sql);
		$ret = array();
		while ($rec = $ilDB->fetchAssoc($res)) {
			$ret[] = $rec[$a_field];
		}

		return $ret;
	}




}


class catDatePeriodFilterType {
	const ID = "dateperiod";
	
	public function getId() {
		return catDatePeriodFilterType::ID;
	}
	
	// config:
	// id
	// label_begin
	// label_end
	// field_begin
	// field_end
	// default_begin
	// default_end
	// as_timestamp (optional, defaults to false)
	// additional_clause (optional, defaults to "")
	
	public function checkConfig($a_conf) {
		if (count($a_conf) !== 8 && count($a_conf) !== 9 && count($a_conf) !== 10) {
			// one parameter less, since type is encoded in first parameter but not passed by user.
			throw new Exception ("catDatePeriodFilterType::checkConfig: expected 7-9 parameters for dateperiod.");
		}
		
		if (count($a_conf) === 8) {
			$a_conf[8] = false;
			$a_conf[9] = "";
		}
		if (count($a_conf) === 9) {
			$a_conf[9] = "";
		}
		
		return $a_conf;
	}
	
	public function isInWhere($a_conf) {
		return true;
	}
	
	public function render($a_tpl, $a_postvar, $a_conf, $a_pars) {
		require_once './Services/Calendar/classes/class.ilCalendarUserSettings.php';
		require_once("Services/Calendar/classes/class.ilCalendarUtil.php");
		
		global $lng, $tpl;

		ilCalendarUtil::initJSCalendar();
		$tpl->addJavaScript('./Services/Form/js/date_duration.js');
		
		$a_tpl->setVariable('DP_LABEL_START', $a_conf[2]);
		$a_tpl->setVariable('DP_LABEL_END', $a_conf[3]);
		
		$a_tpl->setVariable("DP_IMG_CALENDAR", ilUtil::getImagePath("calendar.png"));
		$a_tpl->setVariable("DP_TXT_CALENDAR", $lng->txt("open_calendar"));

		$a_tpl->setVariable("DP_INPUT_FIELDS_START", $a_postvar."[start][date]");
		$a_tpl->setVariable('DP_DATE_FIRST_DAY',ilCalendarUserSettings::_getInstance()->getWeekStart());

		$start = new ilDate($a_pars["start"], IL_CAL_DATE);
		$end = new ilDate($a_pars["end"], IL_CAL_DATE);
		
		$start_info = $start->get(IL_CAL_FKT_GETDATE,"","UTC");
		$a_tpl->setVariable("DP_START_SELECT",
			ilUtil::makeDateSelect(
				$a_postvar."[start][date]",
				$start_info['year'], $start_info['mon'], $start_info['mday'],
				"",
				true,
				array(
					'disabled' => false,
					'select_attributes' => array('onchange' => 'ilUpdateEndDate();')
					),
				false));

		$end_info = $end->get(IL_CAL_FKT_GETDATE,"","UTC");
		$a_tpl->setVariable("DP_END_SELECT",
			ilUtil::makeDateSelect(
				$a_postvar."[end][date]",
				$end_info['year'], $end_info['mon'], $end_info['mday'],
				"",
				true,
				array(
					'disabled' => false
					),
				false));
		
		return true;
	}
	
	public function sql($a_conf, $a_pars) {
		global $ilDB;
	
		if (!$a_conf[8]) {
			return "( ( (".catFilter::quoteDBId($a_conf[5])
						  ." >= ".$ilDB->quote($a_pars["start"], "date")
				  			// this accomodates history tables
				  ."        OR ".catFilter::quoteDBId($a_conf[5])." = '0000-00-00' "
				  ."        OR ".catFilter::quoteDBId($a_conf[5])." = '-empty-'"
				  ."    )"
				  ."   AND ".catFilter::quoteDBId($a_conf[4])
				  			." <= ".$ilDB->quote($a_pars["end"], "date")
				  ."  )"
				  .$a_conf[9]
				  .")"
				  ;
		}
		else {
			$d = new ilDate($a_pars["start"], IL_CAL_DATE);
			$val_s = $d->get(IL_CAL_UNIX);
			$d = new ilDate($a_pars["end"], IL_CAL_DATE);
			$d->increment(ilDateTime::DAY, 1);
			$val_e = $d->get(IL_CAL_UNIX);
			return  "( (".catFilter::quoteDBId($a_conf[5])." >= ".$ilDB->quote($val_s, "integer")
			." AND ".catFilter::quoteDBId($a_conf[5])." <= ".$ilDB->quote($val_e, "integer")." ) "
			.$a_conf[9].")"
			;
		}
	}
	
	public function get($a_pars) {
		return array( "start" => new ilDate($a_pars["start"], IL_CAL_DATE)
					, "end" => new ilDate($a_pars["end"], IL_CAL_DATE)
					);
	}
	
	public function _default($a_conf) {
		return array("start" => $a_conf[6], "end" => $a_conf[7]);
	}
	
	public function preprocess_post($a_post) {
		$s = $a_post["start"]["date"];
		$e = $a_post["end"]["date"];
		$form = "%04d-%02d-%02d";
		return array( "start" => sprintf($form, $s["y"], $s["m"], $s["d"])
					, "end" => sprintf($form, $e["y"], $e["m"], $e["d"])
					);
	}
}
catFilter::addFilterType(catDatePeriodFilterType::ID, new catDatePeriodFilterType());


class catCheckboxFilterType {
	const ID = "checkbox";
	
	public function getId() {
		return catCheckboxFilterType::ID;
	}
	
	// config:
	// id
	// label
	// sql_checked
	// sql_unchecked
	// is_in_having (optional, default to false)
	
	public function checkConfig($a_conf) {
		if (count($a_conf) !== 5 && count($a_conf) !== 6) {
			// one parameter less, since type is encoded in first parameter but not passed by user.
			throw new Exception ("catCheckboxFilterType::checkConfig: expected 4 or 5 parameters for checkbox.");
		}
		
		if (count($a_conf) === 5) {
			$a_conf[] = false;
		}
		
		return $a_conf;
	}
	
	public function render($a_tpl, $a_postvar, $a_conf, $a_pars) {
		require_once("Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php");
		
		$a_tpl->setVariable("PROPERTY_CHECKED", $a_pars ? "checked" : "");
		$a_tpl->setVariable("OPTION_TITLE", $a_conf[2]);
		
		return true;
	}
	
	public function isInWhere($a_conf) {
		return !$a_conf[5];
	}
	
	public function sql($a_conf, $a_pars) {
		global $ilDB;
	
		if ($a_pars && $a_conf[3] !== null) {
			return $a_conf[3];
		}
		
		if($a_conf[4] !== null) {
			return $a_conf[4];
		}
		
		return "";
	}
	
	public function get($a_pars) {
		return $a_pars;
	}
	
	public function _default($a_conf) {
		return false;
	}
	
	public function preprocess_post($a_post) {
		return true;
	}
}
catFilter::addFilterType(catCheckboxFilterType::ID, new catCheckboxFilterType());



class catMultiSelectFilter {
	const ID = "multiselect";
	
	public function getId() {
		return catMultiSelectFilter::ID;
	}
	
	// config:
	// id
	// label
	// field(s)
	// values
	// default_values
	// additional_clause (optional, defaults to "")
	// width (optional, defaults to 160)
	// height (optional, defaults to 75)
	// field type (optional, default to "text")
	// filter-options sorting (defaults to "asc", also possible  "desc", "none")
	
	public function checkConfig($a_conf) {
		if (count($a_conf) < 6) {
			// one parameter less, since type is encoded in first parameter but not passed by user.
			throw new Exception ("catDatePeriodFilterType::checkConfig: expected at least 5 parameters for multiselect.");
		}

		if (count($a_conf) === 6) {
			$a_conf[] = ""; // additional_clause
			$a_conf[] = 200; // width
			$a_conf[] = 160; // height
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
			$a_conf[] = false; //filter-options custom labels
		}
		else if (count($a_conf) === 7) {
			$a_conf[] = 200; // width
			$a_conf[] = 160; // height
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
			$a_conf[] = false; //filter-options custom labels
		}
		else if (count($a_conf) === 8) {
			$a_conf[] = 160; // height
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
			$a_conf[] = false; //filter-options custom labels
		}
		else if (count($a_conf) === 9) {
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
			$a_conf[] = false; //filter-options custom labels
		}
		else if (count($a_conf) === 10) {
			$a_conf[] = "asc"; //filter-options sorting
			$a_conf[] = false; //filter-options custom labels
		}
		else if (count($a_conf) === 11) {
			$a_conf[] = false; //filter-options custom labels
		}
		
		return $a_conf;
	}
	
	public function isInWhere($a_conf) {
		return true;
	}
	
	public function render($a_tpl, $a_postvar, $a_conf, $a_pars) {
		if (count($a_conf[4]) == 0) {
			return false;
		}
		
		$a_tpl->setVariable("MULTI_SELECT_LABEL", $a_conf[2]);
		$a_tpl->setVariable("WIDTH", $a_conf[7]);
		$a_tpl->setVariable("HEIGHT", $a_conf[8]);
		
		$count = 0;
		if($a_conf[10] == "asc") {
			asort($a_conf[4]);
		} else if($a_conf[10] == "desc") {
			arsort($a_conf[4]);
		} else if($a_conf[10] !== "none") {
			throw new ilException($a_conf[1]." catMultiSelectFilter::render: invalid sorting option.");
		}
		// for some unknown reason, the var POST_VAR gets
		// not filled in all places if i call it from catFilter::render.
		if($a_conf[11]) {
			foreach ($a_conf[4] as $title => $value) {
				$a_tpl->setCurrentBlock("multiselect_item");
				$a_tpl->setVariable("CNT", $count);
				$a_tpl->setVariable("OPTION_VALUE", $value);
				$a_tpl->setVariable("OPTION_TITLE", $title);
				$a_tpl->setVariable("POST_VAR", $a_postvar);
				if (in_array($value, $a_pars)) {
					$a_tpl->setVariable("CHECKED", "checked");
				}
				$a_tpl->parseCurrentBlock();
				$count++;
			}

		} else {
			foreach ($a_conf[4] as $title) {
				$a_tpl->setCurrentBlock("multiselect_item");
				$a_tpl->setVariable("CNT", $count);
				$a_tpl->setVariable("OPTION_VALUE", $title);
				$a_tpl->setVariable("OPTION_TITLE", $title);
				$a_tpl->setVariable("POST_VAR", $a_postvar);
				if (in_array($title, $a_pars)) {
					$a_tpl->setVariable("CHECKED", "checked");
				}
				$a_tpl->parseCurrentBlock();
				$count++;
			}
		}
		
		return true;
	}
	
	public function sql($a_conf, $a_pars) {
		global $ilDB;
		if (count($a_pars) == 0) {
			return " TRUE ";
		}
		
		if (is_array($a_conf[3])) {
			$stmts = array();
			foreach($a_conf[3] as $field) {
				$stmts[] = $ilDB->in(catFilter::quoteDBId($field), $a_pars, false, $a_conf[9]);
			}
			return "(".implode(" OR ", $stmts)."  ".$a_conf[6].")";
		}
		return "(".$ilDB->in(catFilter::quoteDBId($a_conf[3]), $a_pars, false, $a_conf[9])." ".$a_conf[6].")";
	}
	
	public function get($a_pars) {
		return $a_pars;
	}
	
	public function _default($a_conf) {
		return $a_conf[5];
	}
	
	public function preprocess_post($a_post) {
		unset($a_post["send"]);
		return $a_post;
	}
}
catFilter::addFilterType(catMultiSelectFilter::ID, new catMultiSelectFilter());



class catTextInputFilter {
	const ID = "textinput";
	
	public function getId() {
		return catTextInputFilter::ID;
	}
	
	// config:
	// id
	// label
	// field(s)
	
	public function checkConfig($a_conf) {
		if (count($a_conf) < 4) {
			// one parameter less, since type is encoded in first parameter but not passed by user.
			throw new Exception ("catDatePeriodFilterType::checkConfig: expected at 3 parameters for multiselect.");
		}

		return $a_conf;
	}
	
	public function isInWhere($a_conf) {
		return true;
	}
	
	public function render($a_tpl, $a_postvar, $a_conf, $a_pars) {
		require_once("Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php");
		
		$a_tpl->setVariable("VALUE", $a_pars);
		$a_tpl->setVariable("OPTION_TITLE", $a_conf[2]);
		
		return true;
	}
	
	public function sql($a_conf, $a_pars) {
		global $ilDB;
		if (count($a_pars) == 0) {
			return " TRUE ";
		}
		
		if (is_array($a_conf[3])) {
			$stmts = array();
			foreach($a_conf[3] as $field) {
				$stmts[] = catFilter::quoteDBId($a_conf[3])." LIKE ".$ilDB->quote("$a_pars%", "text");
			}
			return "(".implode(" OR ", $stmts).")";
		}
		
		return catFilter::quoteDBId($a_conf[3])." LIKE ".$ilDB->quote("$a_pars%", "text");
	}
	
	public function get($a_pars) {
		return $a_pars;
	}
	
	public function _default($a_conf) {
		return "";
	}
	
	public function preprocess_post($a_post) {
		return $a_post;
	}
}
catFilter::addFilterType(catTextInputFilter::ID, new catTextInputFilter());

class catMultiSelectCustomFilter {
	const ID = "multiselect_custom";

	public function getId() {
		return catMultiSelectCustomFilter::ID;
	}

	// config:
	// id
	// label
	// values => statement
	// default_values
	// additional_clause (optional, defaults to "")
	// width (optional, defaults to 160)
	// height (optional, defaults to 75)
	// field type (optional, default to "text")
	// filter-options sorting (defaults to "asc", also possible  "desc", "none")

	public function checkConfig($a_conf) {
		if (count($a_conf) < 6) {
			// one parameter less, since type is encoded in first parameter but not passed by user.
			throw new Exception ("catMultiselectCustomFilter::checkConfig: expected at least 5 parameters for multiselect.");
		}

		if (count($a_conf) === 5) {
			$a_conf[] = ""; // additional_clause
			$a_conf[] = 200; // width
			$a_conf[] = 160; // height
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
		}
		else if (count($a_conf) === 6) {
			$a_conf[] = 200; // width
			$a_conf[] = 160; // height
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
		}
		else if (count($a_conf) === 7) {
			$a_conf[] = 160; // height
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
		}
		else if (count($a_conf) === 8) {
			$a_conf[] = "text"; // type
			$a_conf[] = "asc"; //filter-options sorting
		}
		else if (count($a_conf) === 9) {
			$a_conf[] = "asc"; //filter-options sorting
		}

		return $a_conf;
	}

	public function isInWhere($a_conf) {
		return true;
	}

	public function render($a_tpl, $a_postvar, $a_conf, $a_pars) {
		if (count($a_conf[3]) == 0) {
			return false;
		}

		$a_tpl->setVariable("MULTI_SELECT_LABEL", $a_conf[2]);
		$a_tpl->setVariable("WIDTH", $a_conf[6]);
		$a_tpl->setVariable("HEIGHT", $a_conf[7]);

		$count = 0;
		if($a_conf[10] == "asc") {
			asort($a_conf[3]);
		} else if($a_conf[9] == "desc") {
			arsort($a_conf[3]);
		} else if($a_conf[9] !== "none") {
			throw new ilException($a_conf[1]." catMultiSelectFilter::render: invalid sorting option.");
		}
		// for some unknown reason, the var POST_VAR gets
		// not filled in all places if i call it from catFilter::render.
		foreach ($a_conf[3] as $title => $value) {
			$a_tpl->setCurrentBlock("multiselect_item");
			$a_tpl->setVariable("CNT", $count);
			$a_tpl->setVariable("OPTION_VALUE", $value);
			$a_tpl->setVariable("OPTION_TITLE", $title);
			$a_tpl->setVariable("POST_VAR", $a_postvar);
			if (in_array($value, $a_pars)) {
					$a_tpl->setVariable("CHECKED", "checked");
			}
			$a_tpl->parseCurrentBlock();
			$count++;
		}

		return true;
	}

	public function sql($a_conf, $a_pars) {
		global $ilDB;
		if (count($a_pars) == 0) {
			return " TRUE ";
		}

		return "(".implode(' OR ',$a_pars).")";
	}

	public function get($a_pars) {
		return $a_pars;
	}

	public function _default($a_conf) {
		return $a_conf[4];
	}

	public function preprocess_post($a_post) {
		unset($a_post["send"]);
		return $a_post;
	}
}
catFilter::addFilterType(catMultiSelectCustomFilter::ID, new catMultiSelectCustomFilter());