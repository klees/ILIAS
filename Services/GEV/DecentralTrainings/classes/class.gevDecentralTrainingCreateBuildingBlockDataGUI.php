<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */#

/**
* Collects data for Changing Buildiing Block Infos
*
* @author	Stefan Hecken <stefan.hecken@concepts-and-training.de>
* @version	$Id$
*
*/
require_once("Services/GEV/Utils/classes/class.gevCourseBuildingBlockUtils.php");

class gevDecentralTrainingCreateBuildingBlockDataGUI {

	public function __construct() {
		global $ilCtrl;

		$this->gCtrl = $ilCtrl;
	}

	public function executeCommand() {
		$cmd = $this->gCtrl->getCmd();

		switch($cmd) {
			default:
				$this->$cmd();
		}
	}

	public function changeData() {
		$selected = $_GET["selected"];
		if($_GET["type"] == 0) {
			$this->changeBuildingBlockSelect($selected);
		} else if($_GET["type"] == 1) {
			$this->changeBuildingBlockInfos($selected);
		}
	}

	protected function changeBuildingBlockSelect($selected) {
		$bb = gevBuildingBlockUtils::getPossibleBuildingBlocksByTopicName($selected);
		$this->createJson($bb);
	}

	protected function changeBuildingBlockInfos($selected) {
		$infos = gevBuildingBlockUtils::getBuildingBlockInfosById($selected);
		$this->createJson($infos);
	}

	protected function createJson($data) {
		echo json_encode($data);
		exit;
	}
}