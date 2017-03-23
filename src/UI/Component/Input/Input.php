<?php

namespace ILIAS\UI\Component\Input;

use ILIAS\UI\Component\Input\Validation as V;

/**
 * Common interface shared by all input items and containers.
 */
interface Input extends \ILIAS\UI\Component\Component {

	/**
	 * Combine the input with another and get a new input containing the two.
	 *
	 * @param Input $other
	 * @return Input
	 */
	public function combine(Input $other);


	/**
	 * Mappings are functions that are use to transform data passed as POST array to the
	 * inputs into exploitable data structures. This especially useful for inputs that
	 * are combined from other inputs. Note that mappings may also be chained and so able
	 * to override a default mapping from an input. If no mapping is given, the default
	 * identity mapping is used, passing back the input value itself.
	 *
	 * Example 1, key-value array:
	 * $input = $input->addMapping(
	 *      function($input){
	 *          return ["item 1"=>$input[0],"item 2"=>$input[1]];
	 *      });
	 *
	 * Example 2, directly store into objects:
	 * $structured_output = new stdClass();
	 * $input = $input->addMapping(
	 *      function($input){
	 *          $structured_output->item1 = $input[0];
	 * 	        $structured_output->item2 = $input[1];
	 *      });
	 *
	 * Example 3, mapping of example 2 chained to mapping of example 1:
	 * $input = $input->addMapping(
	 *      function($input){
	 *          return ["item 1"=>$input[0],"item 2"=>$input[1]];
	 *      });
	 * $structured_output = new stdClass();
	 * $input = $input->addMapping(
	 *      function($input){
	 *          $structured_output->item1 = $input["item 1"];
	 * 	        $structured_output->item2 = $input["item 2"];
	 *      });
	 *
	 * @param callable $function
	 * @return Input
	 */
	public function addMapping(Callable $function);

	/**
	 * Apply the added mappings and return the value return by them.
	 *
	 * @return mixed
	 */
	public function map();

	/**
	 * Validations are objects capsuling a function to perform validations. In most cases
	 * it should be sufficient to use one of the standard validations available in the
	 * framework. However it is also possible to use custom validations or to create new
	 * ones to be reused. Validations may be combined. In such a case, all combined
	 * validations must passed for a given input.
	 *
	 * Example 1, using a standard validation checking if input equals 3:
	 * $input = $input->addValidation($factory->input()->validation()->equals(3));
	 *
	 * Example 2, using a custom validation checking if input is smaller than 3:
	 * $input = $input->addValidation(
	 *      $f->input()->validation()->custom(
	 *          function ($input){
	 *              return $input < 3;
	 * },"Not smaller than 3"));
	 *
	 *
	 * @param V\Validation $validation
	 */
	public function addValidation(V\Validation $validation);

	/**
	 * Create a new input with values set from a nested array (mostly from POST).
	 * This input will automatically be validated and passed on to children of this
	 * element if available.
	 *
	 * @param [] $input
	 * @return $this
	 */
	public function withInputFromView($input);

	/**
	 * Get if the input of this input and it's children is valid
	 * @return bool
	 */
	public function isValid();

	/**
	 * Get if the input has been validated (it it has been set by withInputFromView)
	 * @return bool
	 */
	public function isValidated();

	/**
	 * The message collector holds all messages set by failed validations along with a
	 * reference to the inputs that failed the validation
	 *
	 * @return V\ValidationMessageCollector
	 */
	public function getMessageCollector();


	/**
	 * Create a new Input with some value. This will not be validated. Use this to set
	 * the input from some model.
	 *
	 * @param mixed $value
	 * @return Input
	 */
	public function withValue($value);

	/**
	 * Get the value of the input.
	 *
	 * @return mixed
	 */
	public function getValue();

	/**
	 * Create an instance of the input with a set of children.
	 * @param Input[] $children
	 * @return Input
	 */
	public function withChildren($children);

	/**
	 * Get the children of the input.
	 *
	 * @return Input[]
	 */
	public function getChildren();

	/**
	 * Get whether the input has children.
	 *
	 * @return bool
	 */
	public function hasChildren();
}