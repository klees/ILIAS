<?php

namespace ILIAS\UI\Implementation\Component\Input\Container\Form;

use ILIAS\UI\Component\Input\Container as C;
use ILIAS\UI\Implementation\Component\Input\Container\Container;

class Sub extends Container implements C\Form\Section {

    /**
     * @var int
     */
    static $nr_sub = 0;
    /**
     * @param $title
     * @param array|null $items
     */
    public function __construct($items) {
        Sub::$nr_sub++;
        parent::__construct("sub_".Sub::$nr_sub,$items);
    }
}
