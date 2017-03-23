<?php
namespace ILIAS\UI\Implementation\Component\Input\Item\Field;

use ILIAS\UI\Component\Input\Item\Field as F;
use ILIAS\UI\Implementation\Component\Input\Item as I;

/**
 * Class NameAge
 * Todo this is mostly experimenting
 * @package ILIAS\UI\Implementation\Component\Input\Item\Field
 */
class NameAge extends I\Item implements F\Text {

	/**
	 * @inheritdoc
	 */
	public function __construct($name) {
		$children = [new Text($name."_Name"),new Number($name."_Age")];
		parent::__construct($name,$children);
	}
}
