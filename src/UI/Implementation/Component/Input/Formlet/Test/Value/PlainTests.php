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

use ILIAS\UI\Implementation\Component\Input\Formlet\Factory as F;
use PHPUnit_Framework_TestCase;

/**
 * Class PlainValueTest
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\Factory\Test\Value
 */
class PlainValueTest extends PHPUnit_Framework_TestCase {
	use PlainValueTestTrait;

	/**
	 * @return array
	 */
	public function plain_values() {
		$val = rand();
		$rnd = md5(rand());
		$value = F::getFactory()->value()->plain($val, $rnd);
		return [[$value, $val, $rnd]];
	}
}
?>