<?php
/******************************************************************************
 * An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code.
 */
namespace ILIAS\UI\Implementation\Component\Input\Formlet\Factory\Test\Value;

use ILIAS\UI\Implementation\Component\Input\Formlet\Value as Value;

/**
 * Class PlainValueTestTrait
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\Factory\Test\Value
 */
trait PlainValueTestTrait {
	/**
	 * One can get the value out that was stuffed in *(
	 * @dataProvider plain_values
	 */
	public function testInOut(Value $value, $val, $origin) {
		$this->assertEquals($value->get(), $val);
	}
	/**
	 * An ordinary value is not applicable.
	 * @dataProvider plain_values
	 */
	public function testValueIsNotApplicable(Value $value, $val, $origin) {
		$this->assertFalse($value->isApplicable());
	}

	/**
	 * @param Value $value
	 * @param $val
	 * @param $origin
	 * @dataProvider plain_values
	 * @expectedException \ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Exception\Apply
	 */
	public function testValueCantBeApply(Value $value, $val, $origin) {
		$value->apply($value);
	}
	/**
	 * An ordinary value is no error.
	 * @dataProvider plain_values
	 */
	public function testValueIsNoError(Value $value, $val, $origin) {
		$this->assertFalse($value->isError());
	}

	/**
	 * For an ordinary Value, error() raises.
	 * @dataProvider plain_values
	 * @expectedException Exception
	 */
	public function testValueHasNoError(Value $value, $val, $origin) {
		$value->error();
	}
	/**
	 * Ordinary value tracks origin.
	 * @dataProvider plain_values
	 */
	public function testValuesOriginsAreCorrect(Value $value, $val, $origin) {
		$this->assertEquals($value->origin(), $origin ? $origin : null);
	}
}
