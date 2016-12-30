<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor;

use ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value as V;

class Validation{
	/**
	 * @var V\FunctionCallable
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
		$this->method = new V\FunctionCallable($function);
		$this->message_text = $message_text;
	}

	public function getMessageText(){
		return $this->message_text;
	}
	public function getValidationMethod(){
		return $this->method;
	}

	public function invert(){
		$invert = new V\Invert();
		$this->method = $invert->composeWith($this->method);
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function validate($value, $collector, $item){
		$this->method = $this->method->apply($value);

		if($this->method->get()){
			return true;
		}
		$collector->addMessage(new ValidationMessage($this->getMessageText(),
				$item));
		return false;
	}
}