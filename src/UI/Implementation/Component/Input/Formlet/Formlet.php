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
	protected $id = "";

    /**
     * @var FV\FunctionValue
     */
    protected $model_to_view = null;

    /**
     * @var FV\FunctionValue
     */
    protected $view_to_model = null;

	/**
	 * @var V\Validations
	 */
	protected $validations;

	/**
	 * @var V\ValidationMessageCollector
	 */
	protected $message_collector = null;

	/**
	 * @var bool
	 */
	protected $valid = null;

	/**
	 * @var Formlet[]|string
	 */
	protected $content;

	/**
	 * @param $name
	 * @param array $children
	 */
	public function __construct($id, $children = null){
		$this->id = $id;

		if($children){
			$this->content = $children;
			$this->assertNoIdDupblicates();
		}else{
			$this->content = "";
		}

		$this->validations = new V\Validations();
        $this->model_to_view = new FV\Identity();
        $this->view_to_model  = new FV\Identity();
		$this->message_collector = new V\ValidationMessageCollector();
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

    public function modelToView($value){
        $model_to_view = $this->model_to_view->apply($value);
        return $model_to_view->get();
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
	 * @param callable $function
	 * @return Formlet
	 */
	public function addModelToViewMapping(Callable $function){
        $clone = clone $this;
        $clone->model_to_view = $this->model_to_view->apply(
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
				$value[$child->getId()] = $child->extractToModel();
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

	/**
	 * @param $input
	 * @return $this
	 */
	public function withInputFromModel($input){
		$formlet_input = null;
		if(is_array($this->content)) {
			foreach($this->content as $child){
				$formlet_input[$child->getId()] = $input[$child->getId()];
			}

			$formlet_input = $this->modelToView($formlet_input);

			foreach ($this->content as $child) {
				$child->withInputFromModel($formlet_input[$child->getId()]);
			}
		}else{
			$this->content = $this->modelToView($input[$this->getId()]);
		}

		return $this;
	}
}
