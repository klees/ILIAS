<?php

namespace ILIAS\UI\Implementation\Component\Input\Validation;

use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue as F;

class Validations {


	/**
	 * @var FunctionValue[]
	 */
	protected $validations = [];


	/**
	 * Validations constructor.
	 * @param FunctionValue[]
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
	 * @return F\FunctionValue
	 */
	public function getValidations()
	{
		return $this->validations;
	}

}
