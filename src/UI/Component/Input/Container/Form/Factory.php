<?php
namespace  ILIAS\UI\Component\Input\Container\Form;
/**
 * This is how a factory for inputs looks like.
 */
interface Factory {
	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Currently the only form available in ILIAS
	 *
	 * ----
	 * @param string
	 * @param \ILIAS\UI\Component\Input\Item[]
	 * @return  \ILIAS\UI\Component\Input\Container\Form\Standard
	 */
	public function standard($action ="#", $items);

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *    A titled form section subdivides a form allowing users to browse the screen.
	 *    Titled Form Sections are stacked. The stacking reflects perceived relevance
	 *    and is kept consistent across objects thus learners are “familiar” with forms.
	 *   composition: >
	 *     At least one titled form section is used in every form.
	 *     A title states the purpose shared by all settings of the titled form section.
	 *     The section visually groups settings.
	 *   effect: >
	 *     This element has no effect in terms of clicking.
	 *
	 * background: >
	 *    Tidwell ‘Designing Interfaces’ proposed the pattern of
	 *    ‘Titled Sections’ and describes it as: “Define separate sections of content by giving each one a visually strong title,
	 *    separating the sections visually, … “. She further explains that those sections should be grouped by topic or task.
	 *    According to Tidwell is content that is sectioned neatly into chunks easier to grasp since the human
	 *    visual system is always looking for bigger patterns. In ILIAS such sections in a form are called titled form sections.
	 *
	 * rules:
	 *   wording:
	 *       1: A Titled Form Section MUST contain a title.
	 *       2: The title SHOULD summarize the contained settings accurately from a user’s perspective.
	 *       3: The title SHOULD contain less than 30 characters.
	 *       4: The titles MUST be cross-checked with similar forms in other objects or services to ensure consistency throughout ILIAS.
	 *       5: In doubt consistency SHOULD be prioritized over accuracy in titles.
	 *   composition:
	 *       1: Proper Titled Form Sections SHOULD comprise 2 to 5 Settings.
	 *       2: More than 5 Settings SHOULD be split into two areas unless this would tamper with the “familiar” information architecture of forms.
	 *       3: There MUST NOT be a Setting without an enclosing Titled Form Section. If necessary a Titled Form Section MAY contain only one single Setting.
	 *       4: The first and last titled form section of a form MUST contain a “Save” and “Cancel” button for the form. “Save” is left and “Cancel” is right.
	 *       5: >
	 *         In some rare exceptions the Buttons MAY be named differently: if “Save” or “Cancel”  are clearly a
	 *         misleading since the action is more than storing the data into the database. “Send Mail” would be an example of this.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Container\Form\Section
	 */
	public function section();

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Settings can be nested allowing for settings-dependent further configuration.
	 *   composition: >
	 *     Subsettings are underneath the Setting they depend on. The Subsetting is indented to visually confirm said dependence.
	 *   effect: >
	 *     Subsettings are revealed after enabling the selection control of a setting. If the setting is not enabled, the subsetting remains hidden.
	 *
	 * rules:
	 *   usage:
	 *       1: >
	 *         There MUST NOT be a nesting of more than one subsetting (see Jour Fixe comment in feature wiki reference).
	 *         The only exception to this rule is the required quantification of a subsetting by a date or number.
	 *         These exceptions MUST individually accepted by the Jour Fixe.
	 *       2: The title SHOULD summarize the contained settings accurately from a user’s perspective.
	 *       3: The title SHOULD contain less than 30 characters.
	 *       4: The titles MUST be cross-checked with similar forms in other objects or services to ensure consistency throughout ILIAS.
	 *       5: In doubt consistency SHOULD be prioritized over accuracy in titles.
	 *   composition:
	 *       1: Subsettings MUST bear an identifier.
	 *   interaction:
	 *       1: Subsetting MUST NOT be enabled by any other form element than a checkbox or a radio input group.
	 *   ordering:
	 *       1: >
	 *          Subsettings of a setting can be stacked. The most relevant subsetting MUST be the first subsetting in the stack.
	 *          The least relevant comes last in the stack.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Container\Form\Sub
	 */
	public function sub();
}