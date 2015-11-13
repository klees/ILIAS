<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A predicate over a row in the table.
 */
abstract class Predicate {
	/**
	 * @var	Factory
	 */
	protected $factory;
	
	protected function setFactory(Factory $factory) {
		$this->factory = $factory;
	}

	/**
	 * @param	Predicate	$other
	 * @return	Predicate
	 */
	public function _or(Predicate $other) {
		return $this->factory->_or($this, $other);
	}
	
	/**
	 * @param	Predicate	$other
	 * @return	Predicate
	 */
	public function _and(Predicate $other) {
		return $this->factory->_and($this, $other);
	}
	
	/**
	 * @return	Predicate
	 */
	public function _not() {
		return $this->factory->_not($this);
	}
}