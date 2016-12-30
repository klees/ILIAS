<?php
/******************************************************************************
 * An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code.
 */
/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue;
use Exception;
use ReflectionFunction;

/**
 * Class FunctionCallable
 * @package ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value
 */
class FunctionValue implements IFunctionValue {

    /**
     * Number of arguments the passed function carries that is not matched by
     * the set of arguments given for the function
     * @var int
     */
    protected $arity = 0;

    /**
     * Function to be executed as callable.
     * @var Callable
     */
    protected $function;

    /**
     * Set of arguments for the function
     *
     * @var
     */
    protected $args; // array


    /**
     * Create a function value by at least passing it a closure or the name of
     * a function.
     * One could optionally pass an array of arguments for the first arguments
     * of the function to call. This is also used in construction of new function
     * values after apply.
     *
     * ATTENTION: When you pass the name of the function, FunctionValue will not
     * know about optional arguments to your function, that is, it will only be
     * satisfied when all arguments (event optional ones) are provided.
     *
     * @throws Exception
     * @param $function
     * @param array $args
     * @param null $arity
     */
    public function __construct($function, $args = [], $arity = null) {

        if ($arity === null) {
            $refl = new ReflectionFunction($function);
            $this->arity = $refl->getNumberOfParameters() - count($args);

        }
        else {
            $this->arity = $arity - count($args);
        }
        if ($this->arity < 0) {
            throw new Exception("FunctionValue::__construct: more args then parameters.");
        }
        $this->function = $function;
        $this->args = $args;
    }

    /**
     * @inheritdoc
     */
    public function isSatisfied() {
        return $this->arity === 0;
    }

    /**
     * @inheritdoc
     */
    public function get() {
        if(!$this->isSatisfied()){
            throw new Exception("FunctionValue is not satisfied");
        }

        return $this->actualCall();
    }

    /**
     * @inheritdoc
     */
    public function apply($to) {
        if($to instanceof FunctionValue){
            return $this->composeWith($to);
        }
        $args = $this->args;
        $args[] = $to;
        return new self($this->function, $args, $this->arity + count($this->args));
    }

    /**
     * Todo fix for multiple params
     * Compose this function value with another, that is, apply the other function
     * first and then apply the result to this function.
     *
     * @param FunctionValue $other
     * @return FunctionValue
     */
    protected function composeWith(FunctionValue $other) {
        return new self(function($value) use ($other) {
            return $this->apply($other->apply($value)->get())->get();
        });
    }

    /**
     * Executes the actual call
     *
     * @return mixed
     */
    protected function actualCall() {
        return call_user_func_array($this->function, $this->args);
    }
}
