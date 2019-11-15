<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
* Class ilCtrlStructureReader
*
* Reads call structure of classes
*
* @author Alex Killing <alex.killing@gmx.de>
* @version $Id$
*
*/
class ilCtrlStructureReader
{
	var $class_script;
	var $class_childs;

	/**
	 * @var array
	 */
	protected $ctrl_classfile = [];

	/**
	 * @var array
	 */
	protected $ctrl_calls = [];

	function __construct($a_ini_file = null)
	{
		$this->class_script = array();
		$this->class_childs = array();

		$this->ini = $a_ini_file;
	}

	protected function addClassFileEntry(
		string $class,
		string $filename,
		string $comp_prefix = '',
		string $plugin_path = ''
	) {
		$cid = $this->getCid();
		$this->ctrl_classfile[$class] = [
			'class' => $class,
			'filename' => $filename,
			'comp_prefix' => $comp_prefix,
			'plugin_path' => $plugin_path,
			'cid' => $cid
		];
	}

	protected function addCallEntry(
		string $parent,
		string $child,
		string $comp_prefix = ''
	) {
		$this->ctrl_calls[$parent][] = [
			'parent' => $parent,
			'child' => $child,
			'comp_prefix' => $comp_prefix
		];
	}

	protected function getCid() {
		$base_count = count($this->ctrl_classfile);
		return base_convert((string) $base_count, 10, 36);
	}


	public function readAndAppend(
		string $directory,
		string $comp_prefix,
		string $plugin_path,
		string $config_gui_name,
		$il_ctrl
	) {
		list($this->ctrl_classfile, $this->ctrl_calls) = $il_ctrl->getCtrlStructureEntries();

		$this->addCallEntry(
			'ilobjcomponentsettingsgui',
			$config_gui_name,
			$comp_prefix
		);

		$this->readStructure(true, $directory, $comp_prefix, $plugin_path);
		$this->storeStructureArtifact();
	}

	/**
	* read structure
	*/
	function readStructure($a_force = false, $a_dir = "./", $a_comp_prefix = "",
		$a_plugin_path = "")
	{
		require_once('./Services/UICore/classes/class.ilCachedCtrl.php');
		ilCachedCtrl::flush();
		require_once('./Services/GlobalCache/classes/class.ilGlobalCache.php');
		ilGlobalCache::flushAll();

		// prefix for component
		$this->comp_prefix = $a_comp_prefix;

		// plugin path
		$this->plugin_path = $a_plugin_path;

		$this->start_dir = $a_dir;

		$this->read($a_dir);
		$this->buildEntries();
	}

	/**
	 * @param string $path
	 * @return string
	 */
	private function normalizePath(string $path) : string
	{
		return str_replace(['//'], ['/'], $path);
	}

	/**
	* read structure into internal variables
	*
	* @access private
	*/
	function read($a_cdir)
	{
		// check wether $a_cdir is a directory
		if (!@is_dir($a_cdir))
		{
			return false;
		}

		// read current directory
		$dir = opendir($a_cdir);

		while($file = readdir($dir))
		{
			if ($file != "." and
				$file != "..")
			{
				$path = $this->normalizePath($a_cdir."/".$file);
				// directories
				if (@is_dir($path)
					&& $path != './data'
					&& $path != './Customizing'
				) {
					$this->read($path);
				}

				// files
				if (@is_file($path))
				{
					if (preg_match("~^class.*php$~i", $file) || preg_match("~^ilSCORM13Player.php$~i", $file))
					{
						$handle = fopen($path, "r");
						while (!feof($handle)) {
							$line = fgets($handle, 4096);

							// handle @ilctrl_calls
							$pos = strpos(strtolower($line), "@ilctrl_calls");
							if (is_int($pos))
							{
								$com = substr($line, $pos + 14);
								$pos2 = strpos($com, ":");
								if (is_int($pos2))
								{
									$com_arr = explode(":", $com);
									$parent = strtolower(trim($com_arr[0]));

									// check file duplicates
									if ($parent != "" && isset($this->class_script[$parent]) &&
										$this->class_script[$parent] != $path)
									{
										$msg = implode("\n", [
											"Error: Duplicate call structure definition found (Class %s) in files:",
											"- %s",
											"- %s",
											"",
											"Please remove the file, that does not belong to the official ILIAS distribution.",
											"After that invoke 'Tools' -> 'Reload Control Structure' in the ILIAS Setup."
										]);

										throw new \Exception(
											sprintf(
												$msg,
												$parent,
												$this->class_script[$parent],
												$path
											)
										);
									}

									$this->class_script[$parent] = $path;
									$childs = explode(",", $com_arr[1]);
									foreach($childs as $child)
									{
										$child = trim(strtolower($child));
										if (!isset($this->class_childs[$parent]) || !is_array($this->class_childs[$parent]) || !in_array($child, $this->class_childs[$parent]))
										{
											$this->class_childs[$parent][] = $child;
										}
									}
								}
							}

							// handle isCalledBy comments
							$pos = strpos(strtolower($line), "@ilctrl_iscalledby");
							if (is_int($pos))
							{
								$com = substr($line, $pos + 19);
								$pos2 = strpos($com, ":");
								if (is_int($pos2))
								{
									$com_arr = explode(":", $com);
									$child = strtolower(trim($com_arr[0]));
									$this->class_script[$child] = $path;

									$parents = explode(",", $com_arr[1]);
									foreach($parents as $parent)
									{
										$parent = trim(strtolower($parent));
										if (!isset($this->class_childs[$parent]) || !is_array($this->class_childs[$parent]) || !in_array($child, $this->class_childs[$parent]))
										{
											$this->class_childs[$parent][] = $child;
										}
									}
								}
							}

							if (preg_match("~^class\.(.*GUI)\.php$~i", $file, $res))
							{
								$cl = strtolower($res[1]);
								$pos = strpos(strtolower($line), "class ".$cl);
								if (is_int($pos) && (!isset($this->class_script[$cl]) || $this->class_script[$cl] == ""))
								{
									$this->class_script[$cl] = $path;
								}
							}
						}
						fclose($handle);
					}
				}
			}
		}
	}


	private function buildEntries()
	{
		foreach($this->class_script as $class => $script)
		{
			$file = substr($script, strlen($this->start_dir));

			$this->addClassFileEntry(
				$class,
				$file,
				$this->comp_prefix,
				$this->plugin_path
			);
		}
		foreach($this->class_childs as $parent => $v)
		{
			if (is_array($this->class_childs[$parent]))
			{
				foreach($this->class_childs[$parent] as $child)
				{
					if(strlen(trim($child)) and strlen(trim($parent)))
					{
						$this->addCallEntry($parent, $child, $this->comp_prefix);
					}
				}
			}
		}

	}


	public function getStructure() {} //DEPRECATED - there are still calls to this function

	public function getEntries()
	{
		return [
			'classfile' => $this->ctrl_classfile,
			'calls' => $this->ctrl_calls
		];
	}

	protected function storeStructureArtifact()
	{
		$path = dirname(__FILE__)
			."/../../../../"
			.ilCtrlStructureStoredObjective::DATA_FILE;
		$contents = "<?"."php return " .var_export($this->getEntries(), true) . ";";
		file_put_contents($path, $contents);
	}

}
