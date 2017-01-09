<?php

namespace ILIAS\UI\Implementation\Component\Input\Container\Form;

use ILIAS\UI\Component\Input\Container as C;
use ILIAS\UI\Implementation\Component\Input\Container\Container;

class Section extends Container implements C\Form\Section {


    /**
     * @var string
     */
    protected $title = "";

    /**
     * @param $title
     * @param array|null $items
     */
    public function __construct($title, $items) {
        $this->title = $title;
        parent::__construct($title,$items);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
