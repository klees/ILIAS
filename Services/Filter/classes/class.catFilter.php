<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter;

/**
 * A filter on some data.
 *
 * TODO: How should we combine filter? Could i map over the predicate? Over the id,
 * label or description?
 *
 * TODO: It seems to be necessary that there is the possibility to disambiguate ids.
 * Or should we go the formlets way and completely drop the id in favour of an
 * automatic id that is guaranteed to be unique?
 */
abstract class Filter {
	/**
	 * Get the id of the filter.
	 *
	 * @return	string
	 */
	abstract public function id();

	/**
	 * Get the label of the filter.
	 *
	 * TODO: Is this understood to be a lang var or the final text? Or should
	 * it possibly be both?
	 *
	 * @return	string|null
	 */
	abstract public function label();

	/**
	 * Get the description of the filter.
	 *
	 * TODO: Is this understood to be a lang var or the final text? Or should
	 * it possibly be both?
	 *
	 * @return	string|null
	 */
	abstract public function description();

	/**
	 * Get the predicate of this filter.
	 *
	 * @return	Predicates\Predicate
	 */
	abstract public function predicate();
	
	/**
	 * A filter that matches on all data sets that either match this filter
	 * or the other.
	 *
	 * TODO: Is this just a new default filter where the predicates are or'ed? Or 
	 * do we need to use a special class for Or'ed Filters? Same question for _and.
	 *
	 * @param	Filter
	 * @return	Filter
	 */
	abstract public function _or(Filter $other);

	/**
	 * A filter that matches on all data sets that either match this filter
	 * or the other.
	 *
	 * TODO: Is this just a new default filter where the predicates are and'ed? Or 
	 * do we need to use a special class for And'ed Filters? Same question for _or.
	 *
	 * @param	Filter
	 * @return	Filter
	 */
	abstract public function _and(Filter $other);

	/**
	 * A filter that matches on all data sets that do not match this filter.
	 *
	 * TODO: Is this just a new default filter where the predicates are not'ed?
	 *
	 * @return	Filter
	 */
	abstract public function _not();
}