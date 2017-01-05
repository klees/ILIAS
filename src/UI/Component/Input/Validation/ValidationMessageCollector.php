<?php


namespace ILIAS\UI\Component\Input\Validation;

/**
 * Collects messages during validation of filters.
 */
interface ValidationMessageCollector extends \Iterator{

	public function addMessage(ValidationMessage $message);
    public function getMessages();
    public function join(ValidationMessageCollector $collector);
}