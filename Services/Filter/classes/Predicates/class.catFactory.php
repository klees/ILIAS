<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter\Predicates;

/**
 * A predicate over a row in the table.
 */
class Factory {
	// VALUES
	
	public function field($name) {
		$field = new Field($name);
		$field->setFactory($this);
		return $field;
	}
	
	// TODO: We could use some sugar here to infer that value is a list
	// when type = $typ[].
	public function lit($value, $type) {
		$lit = new Literal($v);
		$lit->setFactory($this);
		return $lit;
	}
	
	public function value_list($values, $type) {
		$list = new ValueList($values, $type);
		$list->setFactory($this);
		return $list;
	}
	
	public function current_date() {
		$cd = new CurrentDate();
		$cd->setFactory($this);
		return $cd;
	}
	
	// PREDICATES
	
	public function eq(Value $left, Value $right) {
		$eq = new Eq($left, $right);
		$eq->setFactory($this);
		return $eq;
	}
	
	public function _or(Predicate $left, Predicate $right) {
		// TODO: We could use some magic to flatten nested ors here.
		$or = new _Or($left, $right);
		$or->setFactory($this);
		return $or;
	}
}