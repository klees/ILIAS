<?php

namespace ILIAS\UI\Implementation\Component\Input\Validation;

use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue as F;
use \ILIAS\UI\Component\Input\Item\Item;
use \ILIAS\UI\Component\Input\Validation as V;

/**
 * Class Validation
 * @package ILIAS\UI\Implementation\Component\Input\Validation
 */
class Validation implements V\Validation{

	/**
	 * @var F\FunctionValue
	 */
	protected $method = null;

	/**
	 * @var string
	 */
	protected $message_text = "";

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

	/**
	 * @inheritdoc
	 */
	public function getMessageText(){
		return $this->message_text;
	}

	/**
	 * @inheritdoc
	 */
	public function getValidationMethod(){
		return $this->method;
	}

	/**
	 * @inheritdoc
	 */
	public function invert(){
		$clone = clone $this;
		$f = new F\Factory();
		$invert = $f->invert();
		$clone->method = $invert->apply($this->method);
		return $clone;
	}

	/**
	 * @inheritdoc
	 */
	public function validate($value, V\ValidationMessageCollector $collector, Item $item){
		if($this->method->apply($value)->get()){
			return true;
		}

		$collector->addMessage(new ValidationMessage($this->getMessageText(), $item));
		return false;
	}
}