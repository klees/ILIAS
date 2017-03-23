<?php

namespace ILIAS\UI\Implementation\Component\Input\Container\Form;

use ILIAS\UI\Component\Input\Container as C;
use ILIAS\UI\Implementation\Component\Input\Container\Container;

/**
 * Class Standard
 * @package ILIAS\UI\Implementation\Component\Input\Container\Form
 */
class Standard extends Container implements C\Form\Standard {

    /**
     * @var string
     */
    protected $type = "form";

    /**
     * @var string
     */
    protected $action = "";

    /**
     * @var string
     */
    protected $title = "";

    /**
     * Container constructor.
     * @param $action
     * @param array|null $title
     * @param $items
     */
    public function __construct($action, $title, $items) {
        $this->action = $action;
        $this->title = $title;

        parent::__construct($items);
    }

    /**
     * @inheritdocs
     */
    public function getAction(){
        return $this->action;
    }

    /**
     * @inheritdocs
     */
    public function getTitle()
    {
        return $this->title;
    }
}
