<?php
namespace ILIAS\UI\Component\Input\Validation;

/**
 * Base interface for all validations
 */
interface Validation extends \ILIAS\UI\Component\Component {

    /**
     * Get an inverted version of the validation.
     *
     * @return Validation
     */
    public function invert();

    /**
     * Get the text that is passed if the validation fails.
     * @return mixed
     */
    public function getMessageText();

    /**
     * Get the callable that is used to perform the validation
     *
     * @return mixed
     */
    public function getValidationMethod();
}