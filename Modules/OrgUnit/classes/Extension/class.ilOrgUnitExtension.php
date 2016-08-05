<?php

require_once "Services/Repository/classes/class.ilObjectPlugin.php";
require_once "Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php";

abstract class ilOrgUnitExtension extends ilObjectPlugin {

	/**
	 * @var ilObjOrgUnitTree
	 */
	protected $orguTree;

	/**
	 * @var int
	 */
	protected $parentRefId;

	/**
	 * ilOrgUnitExtension constructor.
	 * @param int $a_ref_id
	 */
	public function __construct($a_ref_id = 0) {
		global $tree;

		parent::__construct($a_ref_id);
		$this->orguTree= ilObjOrgUnitTree::_getInstance();
		$this->parentRefId = $tree->getParentId($a_ref_id?$a_ref_id:$_GET['ref_id']);
	}

	/**
	 * @return null|object
	 * @throws ilPluginException
	 */
	protected function getPlugin() {
		if(!$this->plugin) {
			$this->plugin =
				ilPlugin::getPluginObject(IL_COMP_MODULE, "OrgUnit", "orguext",
					ilPlugin::lookupNameForId(IL_COMP_MODULE, "OrgUnit", "orguext", $this->getType()));
			if (!is_object($this->plugin)) {
				throw new ilPluginException("ilOrgUnitExtension: Could not instantiate plugin object for type " . $this->getType() . ".");
			}
		}
		return $this->plugin;
	}

	/**
	 * @param $ref_id returns all employees of the given org unit.
	 * @param bool $recursively include all employees in the suborgunits
	 * @return int[]
	 */
	public function getEmployees($ref_id, $recursively = false) {
		return $this->orguTree->getEmployees($ref_id, $recursively);
	}

	/**
	 * Get the IDs of the employees of the org unit this plugin belongs to.
	 * @param bool $recursively
	 * @return int[]
	 */
	public function getMyEmployees($recursively = false) {
		return $this->getEmployees($this->parentRefId, $recursively);
	}

	/**
	 * @param $ref_id
	 * @param bool $recursively
	 * @return int[]
	 */
	public function getSuperiors($ref_id, $recursively = false) {
		return $this->orguTree->getSuperiors($ref_id, $recursively);
	}

	/**
	 * @param bool $recursively
	 * @return int[]
	 */
	public function getMySuperiors($recursively = false) {

		return $this->getSuperiors($this->parentRefId, $recursively);
	}

	/**
	 * @return ilObjOrgUnit
	 */
	public function getOrgUnit() {
		return new ilObjOrgUnit($this->parentRefId);
	}



}