<?php

namespace ILIAS\UI\Implementation\Component\Input\Container;

use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\Input\Item as I;
use ILIAS\UI\Implementation\Component\Input\ValidationMessageCollector;
use \ILIAS\UI\Implementation\Component\Input\Formlet as F;

/**
 * One item in the filter, might be composed from different input elements,
 * which all act as one filter input.
 */
class Container extends F\Formlet implements
		\ILIAS\UI\Component\Input\Container\Container{
	use ComponentHelper;

	/**
	 * @var string
	 */
	protected $action = "";


	/**
	 * Container constructor.
	 * @param $action
	 * @param array|null $title
	 * @param $items
	 */
	public function __construct($action, $title, $items) {
		$this->action = $action;
		parent::__construct($title,$items);
	}

	public function getAction(){
		return $this->action;
	}
}
