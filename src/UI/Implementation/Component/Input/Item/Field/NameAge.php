<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */


namespace ILIAS\UI\Implementation\Component\Input\Item\Field;

use ILIAS\UI\Component\Input\Item\Field as F;
use ILIAS\UI\Implementation\Component\Input\Item as I;

class NameAge extends I\Item implements F\Text {

	/**
	 * @inheritdoc
	 */
	public function __construct($name) {
		$children = [new Text($name."_Name"),new Number($name."_Age")];
		parent::__construct($name,$children);
	}
}
