<?php
/******************************************************************************
 * An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code.
 *
 */
namespace ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Exception;
use Exception;

class Apply extends Exception {
	public function __construct($what, $other) {
		parent::__construct("Can't apply $what to $other");
	}
}
