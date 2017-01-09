<?php
namespace ILIAS\UI\Component\Input\Item\Selector;
/**
 * Factory for Repository Selectors
 */
interface Factory {

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Repository Selectors pick a specific repository object to e.g. limit its application, to point to it, to become a starting point or the like.
	 *     They can pick exactly one repository object or multiple.
	 *   composition: >
	 *     Repository selectors contain of two main parts. One part shows the current selection with a triggers to change
	 *     this selction by opening part two, a modal containing the options in a tree to make a new selection from.
	 *   effect: >
	 *     The interaction slightly difers from selecting one to selecing multiple objects.
	 *     </br>-> If one object is selected, the user clicks the "Select"-Link in  the form. ILIAS calls a Roundtrip
	 *     Modal presenting the repository tree. User selects then the object by clicking on an object title (to pick one object).
	 *     ILIAS closes the modal and inserts the selected object into the form.
	 *     </br>-> If one multiple objects are to be selected, then the user also clicks the "Select"-Link in  the form. ILIAS calls a Roundtrip
	 *     Modal presenting the repository tree. User checks all checkboxes to pick more than one object and has to click
	 *     "Save" in order to complete the selction. ILIAS closes the modal and inserts the selected object into the form and adds
	 *     Add/Remove-Glyphs.
	 *
	 * rules:
	 *   usage:
	 *       1: >
	 *          Repository Pickers MAY be used in Forms.
	 *
	 * ----
	 * @param @Todo define structure to pass tree
	 * @return  \ILIAS\UI\Component\Input\Item\Selector\Repository
	 */
	public function repository();



    /**
     * ---
     * description:
     *   purpose: >
     *     A radio group input allows for choosing between mutually exclusive but related options.
     *   composition: >
     *     he radio group has one identifier stating the common
     *     denominator of the mutually exclusive options.
     *   effect: >
     *     Options of a radio group may open a sub form.
     *   rivals:
     *     Select: Select Inputs are used if a selection of more than 5 items has to be displayed.
     *
     * rules:
     *   usage:
     *       1: >
     *          A radio group SHOULD contain 3 to 5 options. They MAY also be used to
     *          select between two options where one is not automatically the inverse of the
     *          other (such as “Order by Date” and “Order by Name”).
     *       2: If more than 5 options are available a Select Input SHOULD be used.
     *   wording:
     *       1: >
     *          Each option in a radio group MUST be labeled. This label SHOULD not consist of more than 5
     *          words. Simple language in the labels is to be used. Technical terms should be avoided
     *          whenever possible or relegated to the by-line.
     *       2: >
     *          If used in forms, the label of the options SHOULD not simply repeat the identifier on the left.
     *          A meaningful labeling SHOULD be chosen instead.
     *   ordering:
     *       1: The presumably most relevant option SHOULD be the first option.
     *       2: Potentially damaging options SHOULD be listed last.
     *
     * ----
     * @param RadioOption[] $radio_options Radio options to be offered by the radio Group
     * @return  \ILIAS\UI\Component\Input\Item\Selector\RadioGroup
     */
    public function radioGroup($id,$label,$radio_options);

    /**
     * ---
     * description:
     *   purpose: >
     *     Todo
     *
     * ----
     * @param RadioOption[] $radio_options Radio options to be offered by the radio Group
     * @return  \ILIAS\UI\Component\Input\Item\Selector\RadioGroup
     */
    public function radioOption($id,$label);
}