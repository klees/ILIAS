<?php

namespace ILIAS\UI\Implementation\Component\Input\Container\Form;

use ILIAS\UI\Component\Input\Container as C;
use ILIAS\UI\Implementation\Component\Input\Container\Container;

/**
 * Class Sub
 * Todo this is mostly experimenting
 * @package ILIAS\UI\Implementation\Component\Input\Container\Form
 */
class Sub extends Container implements C\Form\Section {

    /**
     * Sub constructor.
     * @param array $items
     */
    public function __construct($items) {
        parent::__construct($items);
    }
}
