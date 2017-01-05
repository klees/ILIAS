<?php
namespace ILIAS\UI\Implementation\Component\Input\Validation;

use \ILIAS\UI\Component\Input\Validation as V;

/**
 * Class Factory
 *
 * @package ILIAS\UI\Implementation\Component\Filter
 */
class ValidationMessageCollector implements V\ValidationMessageCollector {
	/**
	 * @var ValidationMessage[]
	 */
	protected $messages = [];
	public function addMessage(V\ValidationMessage $message){
		$this->messages[] = $message;
	}
	public function getMessages(){
		return $this->messages;
	}
	public function join(V\ValidationMessageCollector $collector){
		$this->messages = array_merge($this->message,$collector->getMessages());
	}
	/**
	 * Iterator implementations
	 *
	 * @return bool
	 */
	public function valid() {
		return current($this->messages) !== false;
	}
	public function key() {
		return key($this->messages);
	}
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