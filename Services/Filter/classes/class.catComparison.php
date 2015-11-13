<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter;

/**
 * A comparison between two values.
 */
class Comparison extends Predicate {
	/**
	 * @var	Value
	 */
	protected $left;
	
	/**
	 * @var	Value
	 */
	protected $right;
	
	public function __construct(Value $left, Value $right) {
		$this->left = $left;
		$this->right = $right;
	}
}