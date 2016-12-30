<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor;

use \ILIAS\UI\Implementation\Component\Input\Formlet as F;
use Whoops\Exception\ErrorException;

class Combined extends Formlet{

	/**
	 * @var Formlet[]
	 */
	protected $children = [];


	/**
	 * @param $name
	 * @param array $children
	 */
	public function __construct($name, $children = []){
		$this->name = $name;
		foreach($children as $child){
			//Todo prevent name dublicates in children
			if($child->children){
				$this->children[] = new Combined($this->name."_".$child->name,
						$child->children);
			}else{
				$this->children[] = new Simple($this->name."_".$child->name);
			}

		}
		parent::__construct();
	}

	public function extractToView(){
		$value = [];
		foreach($this->children as $child){
			$value[$child->name] = $child->extractToView();
		}

		return $this->map->viewToModel($value);
	}

	public function extractToModel(){
		if(!$this->valid){
			return "error";
		}

		$value = [];
		foreach($this->children as $child){
			$value[$child->name] = $child->extractToModel();
		}

		return $this->map->viewToModel($value);
	}


	public function withInputFromView($input){
		$this->valid = $this->validation->validate($input,
				$this->message_collector,$this);
		foreach($this->children as $child){
			if(array_key_exists($child->name,$input)){
				$child_input = $input[$child->name];
			}else{
				$child_input = null;
			}
			$child->withInputFromView($child_input);

			if(!$child->isValid()){
				$this->valid = false;
			}
		}
	}

	public function isValid(){
		return $this->valid;
	}
	public function withInputFromModel($input){
		foreach($this->children as $child){
			if(array_key_exists($child->name,$input)){
				$child_input = $input[$child->name];
			}else{
				$child_input = null;
			}
			$child->withInputFromModel($child_input);
		}
	}
}
