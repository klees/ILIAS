<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;


class Constant extends FunctionValue {
    public function __construct($const)
    {
        parent::__construct(function() use ($const){
            return $const;
        });
    }

}
