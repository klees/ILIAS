<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * Base class for Value-like things in Predicates.
 *
 * TODO: Is it possible to represent every combination of and and or
 * and parantheses with this api.
 *	DK: I would think so. If I understand this right, logical relations of predicates provide us with the freedom to set brackets anyway we want.
 */
abstract class Value {
	/**
	 * @var	Factory
	 */
	protected $factory;
	
	protected function setFactory(Factory $factory) {
		$this->factory = $factory;
	}

	// TODO: We could introduce some sugar here, e.g. not taking a Value
	//       and convert non values to literals.
	/**
	 * Get a predicate that this value should be equal to another value.
	 *
	 * @param	Value	$other
	 * @return	Predicate
	 */
	public function eq(Value $other) {
		return $this->factory->eq($this, $other);
	}
	
	/**
	 * Get a predicate that this value should not be equal to another value.
	 *
	 * @param	Value	$other
	 * @return	Predicate
	 */
	public function neq(Value $other) {
		return $this->factory->not($this->eq($other));
	}
	
	/**
	 * Get a predicate that this value should be lower then another value.
	 *
	 * @param	Value	$other
	 * @return	Predicate
	 */
	public function lt(Value $other);
	
	/**
	 * Get a predicate that this value should lower then or equal to another value.
	 *
	 * @param	Value	$other
	 * @return	Predicate
	 */
	public function lt_eq(Value $other);
	
	/**
	 * Get a predicate that this value should be greater than another value.
	 *
	 * @param	Value	$other
	 * @return	Predicate
	 */
	public function gt(Value $other);
	
	/**
	 * Get a predicate that this value should be greater than or equal another value.
	 *
	 * @param	Value	$other
	 * @return	Predicate
	 */
	public function gt_eq(Value $other);
	
	/**
	 * Get a predicate that this value should be null.
	 * DK: This may only refer to a field?
	 *
	 * @return	Predicate
	 */
	public function is_null();
	
	/**
	 * Get a predicate that this value should be like the other value.
	 *
	 * @param	Value	$other
	 * @return	Predicate
	 */
	public function like(Value $other);
	
	/**
	 * Get a predicate that this value should equal another value.
	 *
	 * TODO: What is the correct parameter for this?
	 *
	 * @param	ValueList	$other
	 * @return	Predicate
	 */
	abstract function in(ValueList $other);
}