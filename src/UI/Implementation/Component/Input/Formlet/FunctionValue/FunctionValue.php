<?php
/******************************************************************************
 * This work is inspired by work for Richard Klees. Published under:
 *
 * "An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code."
 *
 * See: https://github.com/lechimp-p/php-formlets
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
     * @var []
     */
    protected $args;


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
        //If $to is itself a FunctionValue a new combined Function value is
        //created
        if($to instanceof FunctionValue){
            return $this->composeWith($to);
        }


        $args = $this->args;
        $count_of_old_args = count($this->args);
        //$to is added to the args of the new function. This will reduce the
        // arity of the new function by one since in the constructor the new
        // arity will be calculated by substracting the count of the new args
        // with the given arity.
        $args[] = $to;
        return new self($this->function, $args, $this->arity +
            $count_of_old_args);
    }

    /**
     * Compose this FunctionValue with another and so create a new combined
     * FunctionValue
     *
     * E.g. $mul_div = $multiply->apply($divide);
     * Function value $multiply will wrap function value divide.
     * $mul_div->apply(6)->apply(3)->apply(5)->get();
     *
     * Executing get will execute the function generated in composeWith. This
     * function will first apply 6 and 3 to divide. Divide is then satisfied.
     * Therefore 5 and the result of divide (2) will be applied to multiply.
     * The result will be 10.
     *
     * @param FunctionValue $other
     * @return FunctionValue
     */
    protected function composeWith(FunctionValue $other) {

        // The arity of the new FunctionValue will be the two existing
        // arities minus one, since the other function will by applied to
        // this during the execution of this function.
        $arity = $this->getArity()+$other->getArity()-1;

        return new self(function() use ($other) {
            //Get the args of this function when called (this is, if get is
            //finally executed on the combined function.
            $args = func_get_args();
            $used_args = [];
            //Apply the arguments as long as necessary on the other (inner
            // function)
            foreach($args as $arg){
                if(!$other->isSatisfied()){
                    $other = $other->apply($arg);
                    $used_args[]  = $arg;
                }
            }

            //Get the args that have not been used for the other function...
            $args = array_diff_assoc($args,$used_args);

            //... and apply them on this
            $temp_this = $this;
            foreach($args as $arg){
                $temp_this = $temp_this->apply($arg);
            }

            //Actual result that will be returned on executing get. The other
            //function is wrapped and executed inside this.
            return $temp_this->apply($other->get())->get();
        },$other->getArgs(),$arity);
    }

    /**
     * Executes the actual call
     *
     * @return mixed
     */
    protected function actualCall() {
        return call_user_func_array($this->function, $this->args);
    }

    /**
     * @return int
     */
    public function getArity()
    {
        return $this->arity;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }
}
