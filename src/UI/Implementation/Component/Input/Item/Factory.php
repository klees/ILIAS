<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Item;

use ILIAS\UI\Component\Input\Item as I;
use ILIAS\UI\Implementation\Component\ComponentHelper;

/**
 * Class Factory
 *
 * @package ILIAS\UI\Implementation\Component\Filter
 */
class Factory implements I\Factory {

	use ComponentHelper;

	/**
	 * @inheritdoc
	 */
	public function field() {
		return new Field\Factory();
	}

	/**
	 * @inheritdoc
	 */
	public function selector() {
	}
}