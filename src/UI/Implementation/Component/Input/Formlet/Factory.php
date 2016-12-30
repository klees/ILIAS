<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */
namespace ILIAS\UI\Implementation\Component\Input\Formlet;

use \ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value as V;
use \ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Collector as C;

/**
 * Class Factory
 *
 */
class Factory {
	/**
	 * Todo: This static function is poisson for testability and proper DI
	 */
	public static function getFactory(){
		return new self();
	}


	public function value() {
		return new V\Factory();
	}

	public function collector() {
		return new C\Factory();
	}
}