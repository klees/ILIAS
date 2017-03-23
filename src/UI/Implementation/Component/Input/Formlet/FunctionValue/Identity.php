<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;

/**
 * Class Identity
 * Function returning argument itself. id(f) = f
 *
 *
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue
 */
class Identity extends FunctionValue {

    /**
     * Identity constructor.
     */
    public function __construct()
    {
        parent::__construct(function($value){
            return $value;
        });
    }

}
