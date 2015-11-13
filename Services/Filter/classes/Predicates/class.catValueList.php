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
	protected $value;
	
	public function __construct($value, $type = null) {
		// TODO: interfer type if not set.
		// TODO: throw exception on unknown types.
		//       types would be: int, string, date
		// TODO: check, that all values are the same type.
		$this->value = $value;
		$this->type = $type;
	}
}