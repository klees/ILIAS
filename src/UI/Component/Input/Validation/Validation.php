<?php


namespace ILIAS\UI\Component\Input\Validation;

/**
 * Todo
 */
interface Validation extends \ILIAS\UI\Component\Component {
    public function invert();

    public function getMessageText();

    public function getValidationMethod();

    public function validate($content_to_validate,
                             \ILIAS\UI\Component\Input\Validation\ValidationMessageCollector  $collector,
                            \ILIAS\UI\Component\Input\Item\Item $item);
}