<?php

namespace ILIAS\UI\Implementation\Component\Input\Item;

use \ILIAS\UI\Implementation\Component\Input\Formlet\Formlet;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\Input\Validation as F;

/**
 * Class Item
 * @package ILIAS\UI\Implementation\Component\Input\Item
 */
class Item  extends Formlet implements \ILIAS\UI\Component\Input\Item\Item{
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
	 * @inheritdoc
	 */
	public function __construct($label,$children = []) {
		$this->checkStringArg("label",$label);
		$this->label = $label;

		parent::__construct($children);
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
	public function required(){
		$clone = clone $this;
		$clone->required = true;
        $clone->addValidation(new F\NotEmpty());
		return $clone;
	}

	public function isRequired(){
		return $this->required;
	}

	/**
	 * Todo this needs to be changed
	 * @param $sub
	 * @return Formlet
	 */
    public function combineWithSubForm($sub){
        return $this->combine($sub);
    }
}
