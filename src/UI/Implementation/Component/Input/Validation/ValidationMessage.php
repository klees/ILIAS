<?php
namespace ILIAS\UI\Implementation\Component\Input\Validation;
/**
 * Todo
 */
class ValidationMessage {
	/**
	 * @var \ILIAS\UI\Component\Input\Item\Item
	 */
	protected $item = null;
	/**
	 * @var string
	 */
	protected $message = "";
	/**
	 * @inheritdoc
	 */
	public function __construct($message,$item) {
		$this->message = $message;
		$this->item = $item;
	}
	public function getMessage(){
		return $this->message;
	}
	public function getItem(){
		return $this->item;
	}
}