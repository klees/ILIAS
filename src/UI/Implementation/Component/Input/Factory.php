<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input;

use ILIAS\UI\Component\Input as I;
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
	public function container() {
		return new Container\Factory();
	}
	/**
	 * @inheritdoc
	 */
	public function item() {
		return new Item\Factory();
	}
	/**
	 * @inheritdoc
	 */
	public function validation() {
		return new Validation\Factory();
	}
}