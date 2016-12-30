<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Validation;

use ILIAS\UI\Component\Input\Validation as I;
use ILIAS\UI\Implementation\Component\ComponentHelper;

/**
 * Class Factory
 *
 * @package ILIAS\UI\Implementation\Component\Filter
 */
class Factory implements \ILIAS\UI\Component\Input\Validation\Factory  {

	use ComponentHelper;

	/**
	 * @inheritdoc
	 */
	public function custom(callable $validation, $message) {
		return new Custom($validation, $message);
	}

	/**
	 * @inheritdoc
	 */
	public function Regex($regex) {
		return new Regex($regex);

	}

	/**
	 * @inheritdoc
	 */
	public function notEmpty() {
		return new notEmpty();

	}
}