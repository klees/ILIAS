<?php
/******************************************************************************
 * This work is inspired/based on work by Richard Klees published under:
 *
 * "An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code."
 *
 * See: https://github.com/lechimp-p/php-formlets
 */
/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Formlet;

use \ILIAS\UI\Component\Input\Input;
use \ILIAS\UI\Component\Input\Validation\Validation;
use \ILIAS\UI\Implementation\Component\Input\Validation\Validations;
use \ILIAS\UI\Implementation\Component\Input\Validation\ValidationMessageCollector;
use \ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue as FV;


/**
 * A formlet represents one part of a form. It can be combined with other formlets
 * to yield new formlets. Formlets are immutable, that is they can be reused in
 * as many places as liked. All methods return fresh Formlets instead of muting
 * the Formlets they are called upon.
 *
 * Todo: Rethink the name. The currently proposed "Formlet" seems to have shifted from
 * the initial concept of formlets.
 *
 * Class Formlet
 * @package ILIAS\UI\Implementation\Component\Input\Formlet
 */
class Formlet implements IFormlet{
	/**
	 * Todo: Improve this! What is name?
	 * @var string
	 */
	protected $name = "test";

	/**
	 * Todo: Improve this! What is type? Ist this needed? Currently only for better
	 * Todo: readability of the array, dom output.
	 * readability
	 * @var string
	 */
	protected $type = "formlet";

    /**
     * Holds all validations errors in the input
     *
     * @var ValidationMessageCollector
     */
    protected $message_collector = null;

	/**
	 * Holds all validations to be applied on the POST data
	 *
	 * @var Validations
	 */
	protected $validations;

	/**
	 * Function value resulting from applying all mappings for this input on each other
	 *
	 * @var FV\FunctionValue
	 */
	protected $mapping = null;

	/**
	 * Holds wheter the function has been validated, and if, wheter it has been validated
	 * successfully
	 *
	 * @var bool|null
	 */
	protected $valid = null;

	/**
	 * Potential children of this input if any.
	 *
	 * @var Formlet[]
	 */
	protected $children = [];

	/**
	 * Value that this input holds (not mapped). If the input holds children, it also
	 * holds the values of the children as array.
	 *
	 * @return string[]|string|null
	 */
	protected $value;


	/**
	 * Formlet constructor.
	 * @param array $children
	 */
	public function __construct($children = []){
		$this->children = $children;
		$this->validations = new Validations();
		$this->message_collector = new ValidationMessageCollector();
        $this->mapping  = FV\Factory::identity();
	}

	/**
	 * Todo: do we really need this?
	 *
	 * @inheritdoc
	 */
	public function combine(Input $other){
		return new self([$this, $other]);
	}

	/**
	 * @inheritdoc
	 */
	public function addMapping(Callable $function){
		$clone = clone $this;
		$fv = FV\Factory::functionValue($function);
		$clone->mapping = $fv->apply($clone->mapping);
		return $clone;
	}

	/**
	 * @inheritdoc
	 */
	public function map(){

		if(!$this->valid){
			//Todo: improve this
			return "error";
		}

		$mapped_values = [];

		foreach($this->getChildren() as $key => $child){
			$mapped_values[] = $child->map();
		}

		//If children did not return anything or if there are no children, map the value
		//the object holds
		if(empty($mapped_values)){
			return $this->mapValue($this->getValue());
		}
		//If the children did return anything map the values returned by the children
		return $this->mapValue($mapped_values);
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
	 * @inheritdoc
	 */
	public function addValidation(Validation $validation){
		$clone = clone $this;
		$clone->validations = $clone->validations->addValidation($validation);
		return $clone;
	}

	/**
	 * @inheritdoc
	 */
	public function isValid(){
		return $this->valid;
	}

	/**
	 * @inheritdoc
	 */
	public function isValidated(){
		return $this->valid !== null;
	}

	/**
	 * @inheritdoc
	 */
	public function getMessageCollector()
	{
		return $this->message_collector;
	}

	/**
	 * @inheritdoc
	 */
	public function extractToView(){
		$children = [];

		//Note, that the recursive nature of this currently takes place in the renderer
		foreach($this->getChildren() as $key => $child){
			$children[] = $child->setName($this->generateName($this->getName(),
					$key,$child->getType()));
		}

		return $children;
	}

	/**
	 * @inheritdoc
	 */
	public function withInputFromView($input){
		$formlet_input = null;

		$clone = $this->withValue($input);

		$clone->valid = $clone->validations->validate($clone->getValue(), $clone->getMessageCollector(),$clone);

		$cloned_children = [];
		foreach($clone->getChildren() as $key => $child){
			$cloned_child = clone $child;

			$child_input = $clone->getValue()[$this->generateKey($key, $cloned_child->getType())];

			$cloned_child = $cloned_child->withInputFromView($child_input);

			if (!$cloned_child->isValid()) {
				$clone->valid = false;
				$clone->message_collector->join($cloned_child->getMessageCollector());
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
	 * @inheritdoc
	 */
	public function withChildren($children)
	{
		$clone = clone $this;
		$clone->children = $children;
		return $clone;
	}

	/**
	 * @inheritdoc
	 */
	public function getChildren(){
		return $this->children;
	}

	/**
	 * @inheritdoc
	 */
	public function hasChildren(){
		return empty($this->getChildren());
	}


	/**
	 * Todo: Improve this! What is name? really rethink the naming
	 *
	 * Generate the name shown in the DOM for this input element. This is needed by the
	 * rendered. Using the formName[parentInput][ChildInput] enables PHP the automatically
	 * read the POST data as nested-array. Note, only the parent can name the child, the
	 * child can not name itself, since it is unaware of it's parents name. Also note,
	 * that names can only be given/generated as soon as all input building has been
	 * completed (since all parents need to be set at this point).
	 *
	 * @param $prefix Part of the name that stems from the parents of the element
	 * @param $counter Number of the element needed to let it be distinguishable from
	 * it's siblings
	 * @param $type E.g. 'Text'. Only used for better readability (we only really need
	 * 'counter')
	 * @return string
	 */
	protected function generateName($prefix,$counter,$type){
		return $prefix."[".$this->generateKey($counter,$type)."]";
	}

	/**
	 * Todo: Improve this!
	 * See description of generateName. This is needed if only the last part of
	 * formName[parentInput][ChildInput] is needed. E.g. to get the value of the
	 * children out of the array of values of the parents
	 *
	 * @param $counter
	 * @param $type
	 * @return string
	 */
	protected function generateKey($counter,$type){
		return $type."_".$counter;
	}


	/**
	 * Todo: Improve this! What is name? really rethink the naming of 'name' ;-)
	 * @param $name
	 * @return Formlet
	 */
	public function setName($name){
		$clone = clone $this;
		$clone->name = $name;
		return $clone;
	}

	/**
	 * Todo: Improve this! What is name? really rethink the naming of 'name' ;-)
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
}
