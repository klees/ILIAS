<?php
namespace ILIAS\UI\Implementation\Component\Input\Validation;

/**
 * Class NotEmpty
 * @package ILIAS\UI\Implementation\Component\Input\Validation
 */
class NotEmpty extends Validation implements \ILIAS\UI\Component\Input\Validation\NotEmpty {

    /**
     * NotEmpty constructor.
     */
    public function __construct() {
        $function = function($input){
            return $input != "";
        };
        parent::__construct($function,"Required Field");
    }
}