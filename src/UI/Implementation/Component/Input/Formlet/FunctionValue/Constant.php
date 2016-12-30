<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;


class Constant extends FunctionCallable {
    public function __construct($const)
    {
        parent::__construct(function($value) use ($const){
            return $const;
        });
    }

}
