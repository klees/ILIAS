<?php

namespace ILIAS\UI\Implementation\Component\Input\Item;

use \ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor as R;
use ILIAS\UI\Implementation\Component\ComponentHelper;

/**
 * One item in the filter, might be composed from different input elements,
 * which all act as one filter input.
 */
class Item  extends R\Simple implements \ILIAS\UI\Component\Input\Item\Item{
	use ComponentHelper;

	/**
	 * @var bool
	 */
	protected $required = false;

	/**
	 * @var string
	 */
	protected $label = "";

	/**
	 * @var string
	 */
	protected $title = "";

	/**
	 * @inheritdoc
	 */
	public function __construct($label) {
		$this->checkStringArg("label",$label);
		$this->label = $label;
		parent::__construct($label);
	}

	/**
	 * @inheritdocs
	 */
	public function getLabel(){
		return $this->label;
	}

	/**
	 * @inheritdocs
	 */
	public function required($required = false){
		//Todo had required validator
		$this->checkBoolArg("required", $required);
		$clone = clone $this;
		$clone->required = $required;
		return $clone;
	}

	public function isRequired(){
		return $this->required;
	}
}
