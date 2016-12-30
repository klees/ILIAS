<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Container;

use ILIAS\UI\Component\Input\Container as C;
use ILIAS\UI\Implementation\Component\ComponentHelper;

/**
 * Class Factory
 */
class Factory implements C\Factory {

	use ComponentHelper;

	/**
	 * @inheritdoc
	 */
	public function filter() {
	}

	/**
	 * @inheritdoc
	 */
	public function form() {
		return new Form\Factory();
	}
}