<?php

/* Copyright (c) 2015 Richard Klees, Extended GPL, see docs/LICENSE */

namespace CaT\Report\Filter;

/**
 * A filter on some data.
 *
 * TODO: How should we combine filter? Could i map over the predicate? Over the id,
 * label or description?
 * DK: In the dynmical case nothing would prevent the user to add filters double. This means the (automatically set) id is the way to go.
 *
 *
 * TODO: It seems to be necessary that there is the possibility to disambiguate ids.
 * Or should we go the formlets way and completely drop the id in favour of an
 * automatic id that is guaranteed to be unique?
 * DK: Autoids seem the secure way, especially if filters are dynamically built.
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
	 *	DK: In ILIAS context this should be lng, of course. In general we may use some object having the txt method, which could be just the identity...
	 * @return	string|null
	 */
	abstract public function label();

	/**
	 * Get the description of the filter.
	 *
	 * TODO: Is this understood to be a lang var or the final text? Or should
	 * it possibly be both?
	 *	DK: In ILIAS context this should be lng, of course. In general we may use some object having the txt method, which could be just the identity...
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
	 * DK: So far all the logic seems to be in predicates and it is complete (any bracketed order of and/or). 
	 * If filter are intended to be just a graphical representation of those (or rather a GUI to build predicates?) why include more logic on this level instead of forwarding the or/and/not methods to the predicate level ?
	 * By the way, does one filter represent a predicate of first order (comparison of values) or a predicate of any order (also combinations of predicates) ?
	 * Which means: Are we building a tree of filters or a tree of predicates, or anything we may choose ? It seems 
	 *  One more question: how much freedom is left to the user ? 
	 *	- May he just fill variables into predicates of provided filters (roughly the system we have now) ? 
	 *	- May he append predefined filtertypes indefinitely based on present fields of a table ? 
	 *	- Or any of these (this is what i assume, would depend on the child object) ?
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
	 *	DK: see _or
	 * @param	Filter
	 * @return	Filter
	 */
	abstract public function _and(Filter $other);

	/**
	 * A filter that matches on all data sets that do not match this filter.
	 *
	 * TODO: Is this just a new default filter where the predicates are not'ed?
	 *
	 *	DK: see _or
	 * @return	Filter
	 */
	abstract public function _not();
}