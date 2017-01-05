<?php
namespace ILIAS\UI\Component\Input\Validation;

/**
 * This is how a factory for inputs looks like.
 */
interface Factory {

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Not empty validators check whether an input has content.
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Validation\NotEmpty
	 */
	public function notEmpty();

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     The equals validator checks wheter the input equals a certain value.
	 * ----
	 * @param $to_be_equaled
	 * @return mixed
	 */
	public function equals($to_be_equaled);

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Regex validators validate content by checking if it complies with a given regular expression.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Validation\Regex
	 */
	public function regex($regex);

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Custom validators accept a user defined function for validating content.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Validation\Custom
	 */
	public function custom(callable $validation, $message);
}