<?php

namespace ILIAS\UI\Component\Input\Item;

/**
 * Interface Item
 * @package ILIAS\UI\Component\Input\Item
 */
interface Item extends \ILIAS\UI\Component\Input\Input {

	/**
	 * Get the label of the Input Item
	 * @return string
	 */
	public function getLabel();

	/**
	 * Create a new Input that requires input to be validated.
	 *
	 * @return Item
	 */
	public function required();

	/**
	 * Get if the input is required
	 *
	 * @return boolean
	 */
	public function isRequired();

}

