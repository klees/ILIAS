<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 *	DK: Combination : predicate x predicate -> predicate
 */
class Combination extends Predicate {
	/**
	 * @var Predicate
	 */
	protected $left;

	/**
	 * @var Predicate
	 */
	protected $right;
	
	public function __construct(Predicate $left, Predicate $right) {
		$this->left = $left;
		$this->right = $right;
	}
}