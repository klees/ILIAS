<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */
namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;

/**
 * Class Factory
 */
class Factory  {

	/**
	 * Syntactic sugar for using apply multiple times:
	 * f1->apply(f2)->apply(f3) = compose(f1,f2,f3);
	 *
	 * Example:
	 * 	$mul = functionValue(function($a,$b){return $a*$b;});
	 *  $div = functionValue(function($a,$b){return $a/$b;});
	 *
	 *  compose($mult,2,$div,12,3)
	 *      ==$mult->apply(2)->apply($div)->apply(12)->apply(3)->get()
	 *      == 2* (12/3)
	 *      == 8
	 *
	 *
	 * @return \ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue\FunctionValue
	 */
	public static function compose(){
		$args = func_get_args();
		$temp = array_shift($args);
		foreach($args as $arg){
			$temp = $temp->apply($arg);
		}
		return $temp;
	}

	/**
	 * Returns a new function value by passing a callable
	 *
	 * @param $function
	 * @param null $arity
	 * @return FunctionValue
	 */
	public static function functionValue($function, $arity = null) {
		return new FunctionValue($function, [], $arity );
	}

	/**
	 * Return a new invert function
	 *
	 * @return Invert
	 */
	public static function invert() {
		return new Invert();
	}

	/**
	 * Returns a new constant function always returning the passed value
	 *
	 * @param $value
	 * @return Constant
	 */
	public static function constant($value) {
		return new Constant($value);
	}

	/**
	 * Returns a new identity function
	 *
	 * @return Identity
	 */
	public static function identity() {
		return new Identity();
	}
}