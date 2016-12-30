<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor;

use \ILIAS\UI\Implementation\Component\Input\Formlet as F;
use Whoops\Exception\ErrorException;

abstract class Formlet{

	/**
	 * @var string
	 */
	protected $name = "";

	/**
	 * @var Map
	 */
	protected $map;

	/**
	 * @var Validations
	 */
	protected $validations;

	/**
	 * @var ValidationMessageCollector
	 */
	protected $message_collector = null;

	/**
	 * @var bool
	 */
	protected $valid = false;


	public function __construct(){
		$this->validations = new Validations();
		$this->map = new Map();
		$this->message_collector = new ValidationMessageCollector();
	}
	/**
	 * @param F\Formlet $other
	 * @return Formlet
	 */
	public function combine(F\Formlet $other){
		return new Combined($this->name."_".$other->name,[$this, $other]);
	}


	/**
	 * @param callable $function
	 * @return Formlet
	 * @throws \Exception
	 */
	public function addValidation(Validation $validation){
		$this->validations->addValidation($validation);
		return $this;
	}

	/**
	 * @param callable $function
	 * @return Formlet
	 */
	public function addViewToModelMapping(Callable $function){
		$comp = $this->map->getViewToModel()->composeWith(
				new F\Internal\Value\FunctionCallable($function)
		);
		$this->map = new Map($this->map->getViewToModel(),$comp);
		return $this;
	}

	/**
	 * @param callable $function
	 * @return Formlet
	 */
	public function addModelToViewMapping(Callable $function){
		$comp = $this->map->getModelToView()->composeWith(
				new F\Internal\Value\FunctionCallable($function)
		);
		$this->map = new Map($comp,$this->map->getModelToView());
		return $this;
	}

	/**
	 * @param Validation $validation
	 */
	public function withValidations(Validations $validations){
		$this->validations = $validations;
	}

	/**
	 * @param Map $map
	 */
	public function withMap(Map $map){
		$this->map = $map;
	}

	/**
	 * @return bool
	 */
	public function isValid(){
		return $this->valid;
	}

	/**
	 * @return ValidationMessageCollector
	 */
	public function getMessageCollector()
	{
		return $this->message_collector;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}



	abstract public function extractToView();

    abstract public function extractToModel();

	abstract public function withInputFromView($input);

	abstract public  function withInputFromModel($input);
}
