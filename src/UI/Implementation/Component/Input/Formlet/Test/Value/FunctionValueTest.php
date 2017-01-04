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

use \ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue\Factory as F;
use PHPUnit_Framework_TestCase;

/**
 * Class PlainValueTest
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\Factory\Test\Value
 */
class FunctionValueTest extends PHPUnit_Framework_TestCase {
	/**
	 * @return array
	 */
	public function getMultiplyFunction() {
		return F::functionValue(function($a,$b){
			return $a*$b;
		});
	}

	/**
	 * @return \ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue\FunctionValue
	 */
	public function getDivideFunction(){
		return F::functionValue(function($a,$b){
			return $a/$b;
		});
	}

	/**
	 * @return \ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue\FunctionValue
	 */
	public function getEqualsFunction(){
		return F::functionValue(function($a,$b){
			return $a==$b;
		});
	}

	public function testGet(){
		$const = F::constant(5);
		$this->assertEquals(5,$const->get());
	}

	public function testSingleApply(){
		$id = F::identity();
		$this->assertEquals(3,$id->apply(3)->get());
	}

	public function testMultiApply1(){
		$mult = $this->getMultiplyFunction();
		$this->assertEquals(12,$mult->apply(3)->apply(4)->get());
	}

	public function testMultiApply2(){
		$equals = $this->getEqualsFunction();
		$this->assertTrue($equals->apply(3)->apply(3)->get());
		$this->assertFalse($equals->apply(3)->apply(4)->get());

	}

	public function testComposeApply1(){
		$invert = F::invert();
		$equals = $this->getEqualsFunction();

		$this->assertTrue($invert
				->apply($equals)
				->apply(4)
				->apply(3)
				->get()
		);
	}

	public function testComposeApply2(){
		$mult = $this->getMultiplyFunction();
		$fun = $mult
				->apply(4)
				->apply($mult)
				->apply(4)
				->apply(3);
		$this->assertEquals(48, $fun->get());

	}

	public function testComposeApply3(){
		$mult = $this->getMultiplyFunction();

		$this->assertEquals(48,$mult
				->apply($mult)
				->apply(4)
				->apply(4)
				->apply(3)
				->get()
		);
	}

	public function testComposeApply4(){
		$mult = $this->getMultiplyFunction();
		$divide = $this->getDivideFunction();

		$this->assertEquals(16,$mult
				->apply(4)
				->apply($divide)
				->apply(12)
				->apply(3)
				->get()
		);
	}

	public function testComposeApply5()
	{
		$mult = $this->getMultiplyFunction();
		$divide = $this->getDivideFunction();

		$this->assertEquals(1, $mult
				->apply($divide)
				->apply(4)
				->apply(12)
				->apply(3)
				->get()
		);
	}

	public function testCompose1(){
		$invert = F::invert();
		$equals = $this->getEqualsFunction();

		$composed = F::compose($invert,$equals,4,3);

		$this->assertTrue($composed->get());
	}

	public function testCompose2(){
		$mult = $this->getMultiplyFunction();
		$composed = F::compose($mult,4,$mult,4,3);

		$this->assertEquals(48, $composed->get());
	}

	public function testCompose3(){
		$mult = $this->getMultiplyFunction();
		$composed = F::compose($mult,$mult,4,4,3);
		$this->assertEquals(48,$composed->get());
	}

	public function testCompose4(){
		$mult = $this->getMultiplyFunction();
		$divide = $this->getDivideFunction();
		$composed = F::compose($mult,4,$divide,12,3);

		$this->assertEquals(16,$composed->get());
	}

	public function testCompose5()
	{
		$mult = $this->getMultiplyFunction();
		$divide = $this->getDivideFunction();
		$composed = F::compose($mult,$divide,4,12,3);
		$this->assertEquals(1, $composed->get());
	}

	public function testComposeVariant1()
	{
		$comp = $this->getEqualsFunction();
		$divide = $this->getDivideFunction();

		$devision_equals_3 = F::compose($comp,3,$divide);

		$this->assertTrue( $devision_equals_3->apply(9)->apply(3)->get());
		$this->assertFalse( $devision_equals_3->apply(12)->apply(3)->get());

	}

	public function testExplodeExample(){
		$explode = F::functionValue("explode", 2);

		$explodeBySpace = $explode->apply(" ");
		$res = $explodeBySpace->apply("foo bar");

		$this->assertEquals(['foo','bar'],$res->get());
	}

	public function testDescriptionExample(){
		$mult = $this->getMultiplyFunction();
		$divide = $this->getDivideFunction();

		$mul_div = $mult->apply($divide);
        $res = $mul_div->apply(6)->apply(3)->apply(5)->get();

		$this->assertEquals(10,$res);
	}

}
?>