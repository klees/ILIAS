<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;

/**
 * Class Constant
 * Function that always returns a given constant. c=2, const(5)=2
 *
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue
 */
class Constant extends FunctionValue {

    /**
     * Constant constructor.
     * @param $const
     */
    public function __construct($const)
    {
        parent::__construct(function() use ($const){
            return $const;
        });
    }
}
