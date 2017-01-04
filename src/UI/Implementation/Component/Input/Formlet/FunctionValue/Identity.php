<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;


class Identity extends FunctionValue {

    public function __construct()
    {
        parent::__construct(function($value){
            return $value;
        });
    }

}
