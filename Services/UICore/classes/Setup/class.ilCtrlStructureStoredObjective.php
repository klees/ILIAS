<?php declare(strict_types=1);

use ILIAS\Setup;

class ilCtrlStructureStoredObjective extends Setup\BuildArtifactObjective
{
	//const TABLE_CLASSFILES = "ctrl_classfile";
	//const TABLE_CALLS = "ctrl_calls";

	const DATA_FILE = "data/ctrlstructure.php";

	/**
	 * @var ilCtrlStructureReader
	 */
	protected $ctrl_reader;

	/**
	 * @var string
	 */
	protected $data_path;

	public function __construct(\ilCtrlStructureReader $ctrl_reader)
	{
		$this->ctrl_reader = $ctrl_reader;
		$this->data_path = './' .self::DATA_FILE;

	}

	public function getArtifactPath() : string
	{
		return $this->data_path;
	}

	public function build() : Setup\Artifact
	{
		$this->ctrl_reader->readStructure(true, './');
		$structure = $this->ctrl_reader->getEntries();
		return new Setup\ArrayArtifact($structure);
	}

}
