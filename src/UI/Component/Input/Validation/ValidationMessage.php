<?php


namespace ILIAS\UI\Component\Input\Validation;

use ILIAS\UI\Component\Input\Input;

/**
 * Interface ValidationMessage
 * @package ILIAS\UI\Component\Input\Validation
 */
interface ValidationMessage {
    /**
     * Get the message itself
     *
     * @return string
     */
	public function getMessage();

    /**
     * Get the input attached to this message
     *
     * @return Input
     */
    public function getItem();
}