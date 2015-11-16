<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A predicate over a row in the table.
 *	DK:Is this really limited to rows, or rather fields, to stick with our nomenclature?
 */
$lif =new Lit(22);
$field = new Field("foo");

$pred1 =  $field->eq($lit);
...
$pred = $pred1->and($pred2);

$filter = new Filter($predx);

__construct {
	$_POST[];
	...
	return $rped;
}

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
	
	/**
	 * Get all parameters in this predicate.
	 *
	 * TODO: What would be the best form to return the parameters. I think
	 * we somehow need a way to disambiguate parameter names.
	 *
	 *	DK: Since a ValueList is planned, why not use it? This is supposed to return a list of some literals, which correspond to user inputs for variables, right?
	 *
	 * @return	array
	 */
	abstract public function parameters();

	/**
	 * Get all fields in this predicate.
	 * DK: Then this would do the same as parameters, every memeber of ValueList being 
	 * @return	array
	 */
	abstract public function fields();
}