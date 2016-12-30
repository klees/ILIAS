<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor;

use \ILIAS\UI\Implementation\Component\Input\Formlet as F;
use Whoops\Exception\ErrorException;

class Simple extends Formlet{
	/**
	 * @var Map
	 */
	protected $map;

	/**
	 * @var string
	 */
	protected $value = "";

	protected $valid = false;

	public function __construct($name){
		$this->name = $name;
		parent::__construct();

	}

	public function extractToView(){
		return $this->value;
	}

	public function extractToModel(){
		if(!$this->valid){
			return "error";
		}

		return $this->map->viewToModel($this->value);
	}

	/**
	 * @param $input
	 * @return $this
	 */
	public function withInputFromView($input){
		$this->valid = $this->validations->validate($input,
				$this->message_collector,$this);
		$this->value = $input;

		return $this;
	}

	/**
	 * @param $input
	 * @return $this
	 */
	public function withInputFromModel($input){

		$this->value = $this->map->modelToView($input);

		$this->valid = true;

		return $this;
	}
}
