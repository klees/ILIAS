<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet;

use \ILIAS\UI\Implementation\Component\Input\Formlet as F;
use \ILIAS\UI\Implementation\Component\Input\Validation as V;
use \ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue as FV;

/**
 * Class Formlet
 * @package ILIAS\UI\Implementation\Component\Input\Formlet
 */
class Formlet implements \ILIAS\UI\Component\Component{
	/**
	 * @var string
	 */
	protected $name = "";

	/**
	 * @var string
	 */
	protected $type = "formlet";

    /**
     * @var FV\FunctionValue
     */
    protected $message_collector = null;

	/**
	 * @var V\Validations
	 */
	protected $validations;

	/**
	 * @var FV\FunctionValue
	 */
	protected $mapping = null;

	/**
	 * @var bool
	 */
	protected $valid = null;

	/**
	 * @var Formlet[]
	 */
	protected $children = [];

	/**
	 * @return string[]|string|null
	 */
	protected $value;


	/**
	 * Formlet constructor.
	 * @param array $children
	 */
	public function __construct($children = []){
		$this->children = $children;
		$this->validations = new V\Validations();
		$this->message_collector = new V\ValidationMessageCollector();
        $this->mapping  = new FV\Identity();
	}

	/**
	 * @param Formlet $other
	 * @return Formlet
	 */
	public function combine(Formlet $other){
		return new self([$this, $other]);
	}

	/**
	 * @param V\Validation $validation
	 * @return Formlet
	 */
	public function addValidation(V\Validation $validation){
		$clone = clone $this;
		$clone->validations->addValidation($validation);
		return $clone;
	}





	/**
	 * @param callable $function
	 * @return Formlet
	 */
	public function addMapping(Callable $function){
		$clone = clone $this;
		$clone->mapping = $clone->mapping->apply(
				new FV\FunctionValue($function)
		);
		return $clone;
	}

	/**
	 * @return bool
	 */
	public function isValid(){
		return $this->valid;
	}

	/**
	 * @return bool
	 */
	public function isValidated(){
		return $this->valid !== null;
	}

	/**
	 * @return V\ValidationMessageCollector
	 */
	public function getMessageCollector()
	{
		return $this->message_collector;
	}

	/**
	 * @return array
	 */
	public function extractToView(){
		$children = [];
		foreach($this->getChildren() as $key => $child){
			$children[] = $child->setName($this->generateName($this->getName(),
					$key,$child->getType()));
		}

		return $children;
	}

	/**
	 * @param $value
	 * @return mixed
	 * @throws \Exception
	 */
	protected function mapValue($value){
		$applied_mapping = $this->mapping->apply($value);
		return $applied_mapping->get();
	}

	/**
	 * @return mixed|string
	 */
	public function map(){

		if(!$this->valid){
			//Todo: improve this
			return "error";
		}

		$mapped_values = [];

		foreach($this->getChildren() as $key => $child){
			$mapped_values[] = $child->map($child->getValue());
		}

		if(empty($mapped_values)){
			return $this->mapValue($this->getValue());
		}
		return $this->mapValue($mapped_values);
	}

	/**
	 * @param Formlet $child
	 * @param $counter
	 * @return Formlet
	 */
	protected function getNamedChild(Formlet $child, $counter){
		return $child->setName($this->generateName($this->getName(), $counter, $child->getType()));
	}

	/**
	 * @param $prefix
	 * @param $counter
	 * @param $type
	 * @return string
	 */
	protected function generateName($prefix,$counter,$type){
		return $prefix."[".$this->generateKey($counter,$type)."]";
	}

	/**
	 * @param $counter
	 * @param $type
	 * @return string
	 */
	protected function generateKey($counter,$type){
		return $type."_".$counter;
	}

	/**
	 * @param $input
	 * @return $this
	 */
	public function withInputFromView($input){
		$formlet_input = null;

		$clone = $this->withValue($input);
		$clone->valid = $clone->validations->validate($clone->getValue(), $clone->getMessageCollector(),$clone);

		$cloned_children = [];
		foreach($clone->getChildren() as $key => $child){
			$cloned_child = clone $child;
			if(!is_array($clone->getValue())||!array_key_exists('formlet_0',
							$clone->getValue())){
				var_dump($_POST);
				var_dump($clone->getValue());
				var_dump($clone);

				exit;
			}
			$child_input = $clone->getValue()[$this->generateKey($key, $cloned_child->getType())];

			$cloned_child = $cloned_child->withInputFromView($child_input);

			if (!$cloned_child->isValid()) {
				$clone->valid = false;
			}

			$cloned_children[] = $cloned_child;
		}

		$clone = $clone->withChildren($cloned_children);

		return $clone;
	}



	/**
	 * @return string
	 */
	public function getType(){
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function getValue(){
		return $this->value;
	}

	/**
	 * @param $value
	 * @return Formlet
	 */
	public function withValue($value)
	{
		$clone = clone $this;
		$clone->value = $value;

		$cloned_children = [];
		foreach($clone->getChildren() as $key => $child){
			$cloned_child = clone $child;
			if(is_array($value) && array_key_exists($key,$value)){
				$cloned_child = $cloned_child->withValue($value[$key]);
			}

			$cloned_children[] = $cloned_child;
		}

		$clone = $clone->withChildren($cloned_children);

		return $clone;
	}

	/**
	 * @param $children
	 * @return Formlet
	 */
	public function withChildren($children)
	{
		$clone = clone $this;
		$clone->children = $children;
		return $clone;
	}

	/**
	 * @return Formlet[]
	 */
	public function getChildren(){
		return $this->children;
	}

	/**
	 * @return bool
	 */
	public function hasChildren(){
		return empty($this->getChildren());
	}

	/**
	 * @param $name
	 * @return Formlet
	 */
	public function setName($name){
		$clone = clone $this;
		$clone->name = $name;
		return $clone;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

}
