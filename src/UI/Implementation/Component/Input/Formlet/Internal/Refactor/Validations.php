<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor;

use ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value as V;

class Validations {


	/**
	 * @var Validation[]
	 */
	protected $validations = [];


	/**
	 * Validations constructor.
	 * @param $validation
	 */
	public function __construct($validations = [])
	{
		$this->validations = $validations;
	}

	public function addValidation(Validation $validation){
		$this->validations[] = $validation;
	}

	public function validate($value, $collector, $item){
		$valid = true;
		foreach($this->validations as $validation){
			if(!$validation->validate($value,$collector, $item)){
				$valid = false;
			}
		}
		return $valid;
	}

	/**
	 * @return V\FunctionCallable
	 */
	public function getValidations()
	{
		return $this->validations;
	}

}
