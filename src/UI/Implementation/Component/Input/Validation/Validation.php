<?php

namespace ILIAS\UI\Implementation\Component\Input\Validation;

use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue as F;
use \ILIAS\UI\Component\Input\Item\Item;
use \ILIAS\UI\Component\Input\Validation as V;

class Validation{
	/**
	 * @var F\FunctionValue
	 */
	protected $method = null;
	/**
	 * @var string
	 */
	protected $message_text = "Test Validation Text";
	/**
	 * @var bool
	 */
	protected $invert = false;

	/**
	 * @inheritdoc
	 */
	public function __construct(Callable $function, $message_text) {
		$f = new F\Factory();
		$this->method = $f->functionValue($function);
		$this->message_text = $message_text;
	}

	public function getMessageText(){
		return $this->message_text;
	}
	public function getValidationMethod(){
		return $this->method;
	}

	public function invert(){
		$f = new F\Factory();
		$invert = $f->invert();
		$this->method = $invert->apply($this->method);
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function validate($value, V\ValidationMessageCollector $collector,
	                         Item $item){
		if($this->method->apply($value)->get()){
			return true;
		}
		$collector->addMessage(new ValidationMessage($this->getMessageText(),
				$item));
		return false;
	}
}