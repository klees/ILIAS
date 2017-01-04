<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */
namespace ILIAS\UI\Implementation\Component\Input\Formlet;


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


	public function formlet($name) {
		return new Formlet($name);
	}

}