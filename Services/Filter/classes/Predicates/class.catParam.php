<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A parameter in a predicate.
 *
 * TODO: We need a similar class for parameters that requires lists.
 * That class should have a common ancestor with ValueList to typehint
 * in Value::in.
 *
 * TODO: The class hierarchy seems to be a bit broken. If i would implement
 * ParameterList it should be a Param and a ValueList, which would not work
 * atm. 
 *	DK: I don't think so, but we may debate, of course: by this logic valueList should extend Value, which it doesn't.
 *	Shouldn't lists work by inclusion (in this case parameter as class members) and not by extension? 
 *	Please also see my comment in catValueList.
 */
final class Param extends Value {
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var	string
	 */
	protected $type;

	public function __construct($name, $type) {
		assert("is_string($name)");
		// TODO: check type.
		$this->name = $name;
		$this->type = $type;
	}
}