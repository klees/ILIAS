<?php


namespace ILIAS\UI\Component\Input\Validation;

/**
 * Todo: Attention: Implementation is currently Mutable. Change this?
 * Collects messages during validation of filters.
 */
interface ValidationMessageCollector extends \Iterator{

    /**
     * Add a message to the collector
     *
     * @param ValidationMessage $message
     */
	public function addMessage(ValidationMessage $message);

    /**
     * Get all messages collected by this collector
     * @return ValidationMessage[]
     */
    public function getMessages();

    /**
     * Add all messages from an other collector to this one.
     *
     * @param ValidationMessageCollector $collector
     */
    public function join(ValidationMessageCollector $collector);
}