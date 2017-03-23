<?php
namespace ILIAS\UI\Implementation\Component\Input\Validation;

use \ILIAS\UI\Component\Input\Validation as V;

/**
 * Todo: Attention: This is currently Mutable. Change this?
 * Class Factory
 *
 * @package ILIAS\UI\Implementation\Component\Filter
 */
class ValidationMessageCollector implements V\ValidationMessageCollector {
	/**
	 * @var ValidationMessage[]
	 */
	protected $messages = [];

	/**
	 * @inheritdoc
	 */
	public function addMessage(V\ValidationMessage $message){
		$this->messages[] = $message;
	}

	/**
	 * @inheritdoc
	 */
	public function getMessages(){
		return $this->messages;
	}

	/**
	 * @inheritdoc
	 */
	public function join(V\ValidationMessageCollector $collector){
		$this->messages = array_merge($this->messages,$collector->getMessages());
	}

	/**
	 * Iterator implementations
	 *
	 * @return bool
	 */
	public function valid() {
		return current($this->messages) !== false;
	}

	/**
	 * @return ValidationMessage
	 */
	public function key() {
		return key($this->messages);
	}

	/**
	 * @return ValidationMessage
	 */
	public function current() {
		return current($this->messages);
	}

	public function next() {
		next($this->messages);
	}

	public function rewind() {
		reset($this->messages);
	}
}