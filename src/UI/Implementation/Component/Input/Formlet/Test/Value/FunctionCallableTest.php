<?php
/******************************************************************************
 * An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code.
 */
namespace ILIAS\UI\Implementation\Component\Input\Formlet\Factory\Test\Value;

require_once("libs/composer/vendor/autoload.php");
require_once("PlainValueTestTrait.php");
require_once("FunctionCallableTestTrait.php");

use ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value\Factory as V;
use \ILIAS\UI\Implementation\Component\Input\Formlet\Factory as F;
use PHPUnit_Framework_TestCase;
use Exception;


function id_test($v) {
	return $v;
}

/**
 * Class FunctionValueTest
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\Factory\Test\Value
 */
class FunctionCallableTest extends PHPUnit_Framework_TestCase {
	use PlainValueTestTrait;
	use FunctionCallableTestTrait;

	protected $id_test_function_name =__NAMESPACE__."\\id_test";

	/**
	 * Check weather compose works as expected: (f . g)(x) = f(g(x))
	 * @dataProvider compose_functions
	 **/
	public function testFunctionComposition($fn, $fn2, $value) {
		$res1 = $fn->composeWith($fn2)->apply($value);
		$tmp = $fn2->apply($value);
		$res2 = $fn->apply($tmp);
		$this->assertEquals($res1->get(), $res2->get());
	}
	/**
	 * Check weather application operator works as expected: f $ x = f x
	 * @dataProvider compose_functions
	 **/
	public function testApplicationOperator($fn, $fn2, $value) {
		$res1 = $fn->composeWith($fn2)->apply($value);
		$tmp = $fn2->apply($value);
		$res2 = $fn->apply($tmp);
		$this->assertEquals($res1->get(), $res2->get());
	}

	public function plain_values() {
		$fn = F::getFactory()->value()->functionCallable($this->id_test_function_name);
		$val = rand();
		$origin = md5($val);
		$value = F::getFactory()->value()->plain($val, $origin);
		return [[$fn->apply($value)->force(), $val, $this->id_test_function_name]];
	}
	public function function_values() {

		$fn = F::getFactory()->value()->functionCallable($this->id_test_function_name);
		$fn2 = $this->alwaysThrows1()
			->catchAndReify("TestException");
		$val = rand();
		$origin = md5($val);
		$value = F::getFactory()->value()->plain($val, $origin);
		return [
				[$fn, $value, 1, $this->id_test_function_name],
				[$fn2, $value, 1, V::ANONYMUS_FUNCTION_ORIGIN]
			];
	}
	public function error_values() {
		$fn = $this->alwaysThrows1()
			->catchAndReify("TestException");
		$fn2 = $this->alwaysThrows2()
			->catchAndReify("TestException");
		$val = rand();
		$origin = md5($val);
		$value = F::getFactory()->value()->plain($val, $origin);
		return array
			// Result of application of throwing function is an error.
		( array($fn->apply($value)->force(), "test exception", V::ANONYMUS_FUNCTION_ORIGIN)
			// Function still catches after application.
		, array($fn2->apply($value)->apply($value)->force(), "test exception", V::ANONYMUS_FUNCTION_ORIGIN)
		);
	}
	public function compose_functions() {
		$times2 = F::getFactory()->value()->functionCallable(function($v) { return $v * 2; });
		return [
			[$times2,
				F::getFactory()->value()->functionCallable("intval",true,null,1),
				F::getFactory()->value()->plain("42")],
			[F::getFactory()->value()->functionCallable("count", true, null,1),
				F::getFactory()->value()->functionCallable("explode", true, null,2, array(" ")),
				F::getFactory()->value()->plain("x x x x")]
		];
	}
	protected function alwaysThrows0 () {
		return F::getFactory()->value()->functionCallable(function () {
			throw new TestException("test exception");
		});
	}
	protected function alwaysThrows1 () {
		return F::getFactory()->value()->functionCallable(function ($a) {
			throw new TestException("test exception");
		});
	}
	protected function alwaysThrows2 () {
		return F::getFactory()->value()->functionCallable(function ($a, $b) {
			throw new TestException("test exception");
		});
	}
}
class TestException extends Exception {
};
?>