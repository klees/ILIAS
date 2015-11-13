<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A field from a table row.
 *
 * TODO: I think we somehow need a way to distinguish fields from the original
 * table from accumulated fields.
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