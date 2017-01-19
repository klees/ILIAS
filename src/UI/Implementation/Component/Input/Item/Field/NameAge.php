<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */


namespace ILIAS\UI\Implementation\Component\Input\Item\Field;

use ILIAS\UI\Component\Input\Item\Field as F;
use ILIAS\UI\Implementation\Component\Input\Item as I;

class NameAge extends I\Item implements F\Text {

	/**
	 * @inheritdoc
	 */
	public function __construct($id,$name) {
		$children = [new Text($id,$name."_Name"),new Number($id,$name."_Age")];
		parent::__construct($id,$name,$children);
	}
}
