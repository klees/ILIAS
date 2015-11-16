<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A list of values.
 */
final class ValueList {
	/**
	 * @var	string
	 */
	protected $type;
	
	/**
	 * @var	mixed
	 */
	protected $values = array();
	
	public function __construct() {
		// TODO: interfer type if not set.
		// TODO: throw exception on unknown types.
		//       types would be: int, string, date
		// TODO: check, that all values are the same type.

		// DK: 	I don't get it, why do we need any types here? Why not use a general value list, which may contain anything derived from value? 
		// 		Then one may add any values-objects here and those would take care of their types themselves?
	}

	public function addValue(Value $a_value) {
		$this->values[] 
	}
}