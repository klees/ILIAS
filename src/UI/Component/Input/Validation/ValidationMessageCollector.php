<?php


namespace ILIAS\UI\Component\Input\Validation;

/**
 * Collects messages during validation of filters.
 */
interface ValidationMessageCollector extends \Iterator{

	public function withMessage(\ILIAS\UI\Component\Input\Validation\ValidationMessage $message);
    public function getMessages();
    public function join(\ILIAS\UI\Component\Input\Validation\ValidationMessageCollector $collector);
}