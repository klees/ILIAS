<?php

/* Copyright (c) 2016 Fabian Schmid <fs@studer-raimann.ch> Extended GPL, see docs/LICENSE */
/* Copyright (c) 2016 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Input\Item;

/**
 * One item in the filter, might be composed from different input elements,
 * which all act as one filter input.
 */
interface Item extends \ILIAS\UI\Component\Component {

	/**
	 * @return string
	 */
	public function getLabel();

	/**
	 * Set the default value to be displayed.
	 *
	 * @param   mixed $default
	 */
	public function required();

	/**
	 * @return boolean
	 */
	public function isRequired();

}

