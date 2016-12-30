<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Container\Form;

use ILIAS\UI\Component\Input\Container\Form as F;
use ILIAS\UI\Implementation\Component\ComponentHelper;

/**
 * Class Factory
 */
class Factory implements F\Factory {

	use ComponentHelper;

	/**
	 * @inheritdoc
	 */
	public function standard($action = "#", $items) {
		return new Standard($action, $items);
	}

	/**
	 * @inheritdoc
	 */
	public function section() {
	}

	/**
	 * @inheritdoc
	 */
	public function sub() {
	}
}