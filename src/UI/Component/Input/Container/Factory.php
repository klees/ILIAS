<?php
namespace ILIAS\UI\Component\Input\Container;
/**
 * This is how a factory for inputs container looks like.
 */
interface Factory {
	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Filters enable to add and remove criteria for displaying or hiding items in a list or table.
	 *   composition: >
	 *     Filters are composed of the input selectors and fields for composing the individual criteria for the filter along with an “Apply Filter” Button to apply Filter Button.
	 *
	 * rules:
	 *   accessibility:
	 *     1: Every Input Element in every Input Collection MUST be accessible by keyboard.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Container\Filter\Filter
	 */
	public function filter();

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Forms are used for creating content of sub-items or for configuring objects or services.
	 *   composition: >
	 *     A form is divided into areas, which are further divided into sections containing settings with selection or input field controls.
	 *   effect: >
	 *     Users manipulate input in settings and save the form to apply the settings to the object or service.
	 *
	 * context: >
	 *     Forms are usually placed in a tabbed content area.
	 *
	 * rules:
	 *   usage:
	 *     1: Forms MUST NOT be used on the same content screen as tables.
	 *     2: Forms MUST NOT be used on the same content screen as toolbars.
	 *   composition:
	 *     1: Each form MUST contain at least one titled form section.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Container\Form\Factory
	 */
	public function form();

}