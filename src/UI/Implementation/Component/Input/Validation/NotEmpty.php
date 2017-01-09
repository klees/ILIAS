<?php
namespace ILIAS\UI\Implementation\Component\Input\Validation;


/**
 * Todo
 */
class NotEmpty extends Validation implements \ILIAS\UI\Component\Input\Validation\NotEmpty {
    public function __construct() {
        $function = function($input){
            return $input != "";
        };
        parent::__construct($function,"Required Field");
    }
}