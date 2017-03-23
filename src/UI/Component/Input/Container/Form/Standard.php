<?php
namespace  ILIAS\UI\Component\Input\Container\Form;

/**
 * Interface Standard
 * @package ILIAS\UI\Component\Input\Container\Form
 */
interface Standard extends \ILIAS\UI\Component\Input\Container\Container {

    /**
     * Get the forms action
     *
     * @return string
     */
    public function getAction();


    /**
     * Get the title of the form
     *
     * @return string
     */
    public function getTitle();
}