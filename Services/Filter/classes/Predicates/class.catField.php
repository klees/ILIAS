<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A field from a table row.
 */
final class Field extends Value {
	/**
	 * @var	string
	 */
	protected $name;
	
	public function __construct($name) {
		assert("is_string($name)");
		// TODO: Maybe check $name for compliance with (table.)name
		$this->name = $name;
	}
}