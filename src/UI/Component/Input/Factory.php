<?php
namespace ILIAS\UI\Component\Input;
/**
 * This is how a factory for inputs looks like.
 */
interface Factory {
	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Input Containers are aggregate controls in order to gather input from the user. Examples are Forms or Filters.
	 *   composition: >
	 *     They consist of one or multiple Input items.
	 *   effect: >
	 *      Input entered into in Input Collection is saved by clicking on an Interaction Trigger, mostly a Button.
	 *
	 * rules:
	 *   accessibility:
	 *     1: Every Input Item in every Input Collection MUST be accessible by keyboard.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Container\Factory
	 */
	public function container();

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     An Input Item is used to collect one specific piece of information from the  user.
	 *   composition: >
	 *     A Input item is composed of an label naming the concrete item, might contain a by line describing it and an input control
	 *     the collect the actual data from the user.
	 *   effect: >
	 *     The effect depends on the control-type of the input element.
	 *
	 * rules:
	 *   composition:
	 *     1: A Input Item MUST use a label.
	 *   wording:
	 *     1: >
	 *      A label MUST be composed of one single term or a very short phrase.
	 *      The label is an eye catcher for users skimming the form.
	 *     2: >
	 *       A label MUST avoid lingo. Intelligibility by occasional users is prioritized over technical accuracy.
	 *       The accurate technical expression is to be mentioned in the by-line.
	 *     3: >
	 *       An label MUST make a positive statement.
	 *       If the purpose of the setting is inherently negative, use Verbs as “Limit..”, “Lock..”.
	 *     4: >
	 *       Wording and structure (sequence and control) MUST be consistent with identifiers in other objects.
	 *       If you feel a wording needs to be changed, then you MUST propose it to the JF.
	 *     5: A by-line (explanatory text) MAY be added below the input element.
	 *     6: >
	 *       If by-lines are provided they MUST be informative, not merely repeating the identifier’s or input element’s content.
	 *       If no informative description can be devised, no description is needed.
	 *     7: >
	 *       A by-line MUST clearly state what effect the Setting produces and explain, why this might be important and what it can be used for.
	 *     8: >
	 *       Bulk by-lines underneath a stack of option explaining all of the options in one paragraph MUST NOT be used.
	 *     9: >
	 *       A by-line SHOULD NOT address the user directly. Addressing users directly is reserved for cases of high risk of severe mis-configuration.
	 *     10: >
	 *       A by-line MUST be grammatically complete sentence with a period (.) at the end.
	 *     11: >
	 *       By-lines SHOULD be short with no more than 25 words.
	 *     12: >
	 *       By-lines SHOULD NOT use any formatting in descriptions (bold, italic or similar).
	 *     13: >
	 *       If by-lines refer to other tabs or options or tables by name, that reference should be made in quotation marks:
	 *       ‘Info’-tab /  "Info"-Reiter, button ‘Show Test Results’ / Button "Testergebnisse anzeigen",
	 *       ‘Table of Detailed Test Results’  /  "Tabelle mit detaillierten Testergebnissen".
	 *       Use proper quotation marks, not apostrophs.
	 *       Use single quotation marks for english language and double quotation maks for german language.
	 *     14: >
	 *       By-lines MUST NOT feature parentheses since they greatly diminish readability.
	 *     15: >
	 *       By-lines SHOULD NOT start with terms such as: If this option is set … If this setting is active …
	 *       Choose this setting if … This setting … Rather state what happens directly: Participants get / make  / can …
	 *       Point in time after which…. ILIAS will monitor… Sub-items xy are automatically whatever .. Xy will be displayed at place.
	 *
	 *
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Item\Factory
	 */
	public function item();

	/**
	 * ---
	 * description:
	 *   purpose: >
	 *     Validation components are used to validate various kinds of inputs.
	 *   composition: >
	 *     Validation components are usually placed inside of other inputs components. They can not work on their own.
	 *   effect: >
	 *     Validations show whether an input is (or will be) accepted by the system or is (or was) rejected.
	 *
	 * ----
	 * @return  \ILIAS\UI\Component\Input\Validation\Factory
	 */
	public function validation();

}