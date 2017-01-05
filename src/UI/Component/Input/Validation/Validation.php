<?php


namespace ILIAS\UI\Component\Input\Validation;

use \ILIAS\UI\Component\Input\Item\Item;

/**
 * Todo
 */
interface Validation extends \ILIAS\UI\Component\Component {
    public function invert();

    public function getMessageText();

    public function getValidationMethod();

    public function validate($value,
                             ValidationMessageCollector  $collector,
                             Item $item);
}