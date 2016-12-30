<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;


/**
 * Function value inverting possible input. a = Invert(Invert(a))
 *
 * Class Invert
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value
 */
class Invert extends FunctionValue {

    public function __construct()
    {
        parent::__construct(function($value){
            return !$value;
        });
    }

}
