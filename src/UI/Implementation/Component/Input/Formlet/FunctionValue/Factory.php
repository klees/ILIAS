<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */
namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;

/**
 * Class Factory
 *
 */
class Factory  {
	public static function compose(){
		$args = func_get_args();
		$temp = array_shift($args);
		foreach($args as $arg){
			$temp = $temp->apply($arg);
		}
		return $temp;
	}

	public static function functionValue($function, $arity = null) {
		return new FunctionValue($function, [], $arity );
	}

	public static function invert() {
		return new Invert();
	}

	public static function constant($value) {
		return new Constant($value);
	}

	public static function identity() {
		return new Identity();
	}
}