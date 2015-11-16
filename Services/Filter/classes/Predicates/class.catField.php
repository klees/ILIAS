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
		// TODO: Maybe check $name for compliance with (table.)name.
		// DK: Well, this may also be a way to tell, wether it is an original field.
		// Here also the way of building the query becomes important. 
		// Thinking about this, all the field-type values are known from the begining, as soon the rest of the query is built. 
		// Thus one should take care of this problem at the stage of query building.
		$this->name = $name;
	}
}