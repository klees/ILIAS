<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A literal value in a predicate.
 */
final class Literal extends Value {
	/**
	 * @var	string
	 */
	protected $type;
	
	/**
	 * @var	array
	 */
	protected $values;
	
	public function __construct($value, $type = null) {
		// TODO: interfer type if not set.
		// TODO: throw exception on unknown types.
		//       types would be: int, string, date
		$this->value = $value;
		$this->type = $type;
	}
}