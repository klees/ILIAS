<?php
/******************************************************************************
 * An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code.
 *
 */
/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;

/**
 * Function Values work around the problem, that functions could not be used as
 * ordinary values easily in PHP.
 *
 * A value either wraps a plain value in an underlying PHP-Representation or
 * is a possibly curried function that could be applied to other values.
 */
interface IFunctionValue {

	/**
	 * Returns wheter the has been applied enough times to have a result.
	 *
	 * @return bool
	 */
	public function isSatisfied();


	/**
	 * Get the value from the result of the function if it is
	 * satisfied. Throw otherwise.
	 *
	 * If the function is satisfied get the result.
	 *
	 * @return mixed
	 */
	public function get();

	/**
	 * Apply the function to a value, creating a new function value with one
	 * less arity.
	 *
	 * @param $to
	 * @return FunctionValue
	 */
	public function apply($to);
}