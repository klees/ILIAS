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
	protected $id = 0;

    /**
     * @var FV\FunctionValue
     */
    protected $collector = null;

	/**
	 * @var V\Validations
	 */
	protected $validations;

	/**
	 * @var V\ValidationMessageCollector
	 */
	protected $view_to_model = null;

	/**
	 * @var bool
	 */
	protected $valid = null;

	/**
	 * @var Formlet[]|string
	 */
	protected $content;

	function setId($id){
		$clone = clone $this;
		$clone->id = $id;
		return $clone;
	}
	/**
	 * @param $name
	 * @param array $children
	 */
	public function __construct($content = "",$children = null){
		if($children){
			$children_clones = [];
			$i = 0;
			foreach($children as $child){
				$children_clones[] = $child->setId($this->getId()."_".$i);
				$i++;
			}


			$this->content = $children_clones;
			$this->assertNoIdDupblicates();
		}else{
			$this->content = $content;
		}

		$this->validations = new V\Validations();
		$this->message_collector = new V\ValidationMessageCollector();

        $this->view_to_model  = new FV\Identity();
	}

	protected function assertNoIdDupblicates(){
		$dublicate_names = [];
		foreach(array_count_values($this->getUsedIds()) as $name => $number){
			if($number > 1) {
				$dublicate_names[] = $name;
			}
		}

		if(count($dublicate_names)>0){
			throw new \Exception("Name Duplicate detected: "
					.$dublicate_names[0]);
		}
	}

	/**
	 * @return array
	 */
	public function getUsedIds()
	{
		$names = [$this->getId()];
		if(is_array($this->content)){
			foreach($this->content as $child){
				$names = array_merge($names,$child->getUsedIds());
			}
		}

		return $names;
	}

	/**
	 * @param $id
	 * @return Formlet
	 */
	public function createClone($id){
		$clone = clone $this;
		$clone->id = $id;
		return $clone;
	}

	/**
	 * @param Formlet $other
	 * @return Formlet
	 */
	public function combine(Formlet $other){
		return new self($this->id."_".$other->id,[$this, $other]);
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


    public function viewToModel($value){
        $view_to_model = $this->view_to_model->apply($value);
        return $view_to_model->get();
    }
	/**
	 * @param callable $function
	 * @return Formlet
	 */
	public function addViewToModelMapping(Callable $function){
		$clone = clone $this;
        $clone->view_to_model = $this->view_to_model->apply(
            new FV\FunctionValue($function)
        );
        return $clone;
	}

	/**
	 * @param V\Validations $validations
	 * @return $this
	 */
	public function withValidations(V\Validations $validations){
		$this->validations = $validations;
		return $this;
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
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @return array
	 */
	public function extractToView(){
		return $this->content;
	}

	/**
	 * @return mixed|string
	 */
	public function extractToModel(){

		if(!$this->valid){
			return "error";
		}

		$value = $this->content;

		if(is_array($this->content)){
			$value = [];
			foreach($this->content as $child){
				$view_out = $child->extractToModel();
				if(is_array($view_out)){
					foreach($view_out as $key => $out){
						$value[$key] = $out;
					}
				}else{
					$value[$child->getId()] = $view_out;

				}
			}
		}


		return $this->viewToModel($value);
	}


	/**
	 * @param $input
	 * @return $this
	 */
	public function withInputFromView($input){
		$formlet_input = null;
		if(is_array($this->content)) {
			foreach($this->content as $child){
				$formlet_input[$child->getId()] = $input[$child->getId()];
			}

		}else{
			$formlet_input = $input[$this->getId()];
			$this->content = $formlet_input;
		}

		$this->valid = $this->validations->validate($formlet_input,
				$this->message_collector,$this);

		if(is_array($this->content)) {
			foreach ($this->content as $child) {
				$child->withInputFromView($input);
				if (!$child->isValid()) {
					$this->valid = false;
				}
			}
		}

		return $this;
	}
}
