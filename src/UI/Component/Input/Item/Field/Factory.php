<?php

namespace ILIAS\UI\Component\Input\Item\Field;

/**
 * Factory for Filters.
 */
interface Factory {
	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     A Text Field Input field captures a one-line input of text .
	 *   composition: >
	 *     This default text input field is restricted to one line of text.
	 *
	 * rules:
	 *   usage:
	 *     1: Text Field Inputs MUST NOT be used for choosing from predetermined options choices.
	 *     2: Text Field Inputs SHOULD only be used when any kind of input can be entered.
	 *     3: Text Field Inputs MUST NOT be used for numeric input, a Number Field Input is to be used instead.
	 *     4: Text Field Inputs MUST NOT be used for letter-only input, an Alphabet Field Input is to be used instead.
	 *   interaction:
	 *     1: Text Field Inputs MUST have a limited the number of accepted characters to be accepted if only a limited number of characters is to be stored to the database.
	 *     2: Text Field Inputs MUST NOT provide the default value in the description because it is at high risk to be missed.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Item\Field\Text
	 */
	public function text($label);
}