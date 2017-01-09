<?php

namespace ILIAS\UI\Implementation\Component\Input\Item;

use \ILIAS\UI\Implementation\Component\Input\Formlet\Formlet;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\Input\Validation as F;

/**
 * One item in the filter, might be composed from different input elements,
 * which all act as one filter input.
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
	public function __construct($id, $label,$children = []) {
		$this->checkStringArg("id",$id);
		$this->checkStringArg("label",$label);
		$this->label = $label;

		parent::__construct($id,$children);
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

    public function combineWithSubForm($sub){
        return $this->combine($sub);
    }
}
