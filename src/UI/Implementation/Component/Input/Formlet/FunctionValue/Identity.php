<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;


class Identity extends FunctionValue {

    public function __construct()
    {
        parent::functionValue(function($value){
            return $value;
        });
    }

}
