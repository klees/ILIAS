<?php

namespace ILIAS\UI\Implementation\Component\Input\Validation;

use ILIAS\UI\Component\Input\Validation\Validation;
use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue as F;

/**
 * Class Validations
 *
 * Set of validations that is held by some input
 *
 * @package ILIAS\UI\Implementation\Component\Input\Validation
 */
class Validations {

	/**
	 * @var Validation[]
	 */
	protected $validations = [];

	/**
	 * Validations constructor.
	 * @param Validation[] $validations
	 */
	public function __construct($validations = [])
	{
		$this->validations = $validations;
	}

	/**
	 * @param Validation $validation
	 * @return Validations
	 */
	public function addValidation(Validation $validation){
		$clone = clone $this;
		$clone->validations[] = $validation;
		return $clone;
	}

	/**
	 * Performs the actual validation with all the validations given in sequence.
	 *
	 * @param $value
	 * @param $collector
	 * @param $item
	 * @return bool
	 */
	public function validate($value, $collector,$item){
		$valid = true;
		foreach($this->validations as $validation){
			if(!$validation->validate($value,$collector,$item)){
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
