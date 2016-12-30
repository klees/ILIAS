<?php
/******************************************************************************
 * An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code.
 */
namespace ILIAS\UI\Implementation\Component\Input\Formlet\Factory\Test\Value;

use ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Exception as E;
use Exception;

trait FunctionCallableTestTrait {
    /**
     * One can't get a value out of an unsatisfied function value.
     * @dataProvider function_values
     * @expectedException \ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Exception\Get
     */
    public function testNotSatisfiedNoValue($fn, $value, $arity, $origin) {
        if ($arity !== 0) {
            $fn->get();
        }
        else {
            throw new E\Get("mock");
        }
    }
    /**
     * Function value is applicable.
     * @dataProvider function_values
     */
    public function testFunctionIsApplicable($fn, $value, $arity, $origin) {
        if ($arity !== 0) {
            $this->assertTrue($fn->isApplicable());
        }
    }
    /**
     * One can apply function value to ordinary values.
     * @dataProvider function_values
     */
    public function testFunctionCanBeApplied($fn, $value, $arity, $origin) {
        if ($arity > 0) {
            $this->assertInstanceOf
            ('\ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value\FunctionCallable', $fn->apply($value));
        }
    }
    /**
     * A function value is no error.
     * @dataProvider function_values
     */
    public function testFunctionIsNoError($fn, $value, $arity, $origin) {
        $this->assertFalse($fn->isError());
    }
    /**
     * For function value, error() raises.
     * @dataProvider function_values
     * @expectedException Exception
     */
    public function testFunctionHasNoError($fn, $value, $arity, $origin) {
        $fn->error();
    }
    /**
     * Function value origin defaults to empty array.
     * @dataProvider function_values
     */
    public function testFunctionsOriginsAreCorrect($fn, $value, $arity, $origin) {
        $this->assertEquals($fn->origin(), $origin);
    }
    /**
     * Functions has expected arity of $arity.
     * @dataProvider function_values
     */
    public function testFunctionsArityIsCorrect($fn, $value, $arity, $origin) {
        $this->assertEquals($fn->arity(), $arity);
    }
    /**
     * Functions is not satisfied or has arity 0.
     * @dataProvider function_values
     */
    public function testFunctionSatisfaction($fn, $value, $arity, $origin) {
        if ($arity === 0) {
            $this->assertTrue($fn->isSatisfied());
        }
        else {
            $this->assertFalse($fn->isSatisfied());
        }
    }
    /**
     * After $arity applications, function is satisfied.
     * @dataProvider function_values
     */
    public function testFunctionIsSatisfiedAfterEnoughApplications($fn, $value, $arity, $origin) {
        $tmp = $this->getAppliedFunction($fn, $value, $arity);
        $this->assertTrue($tmp->isSatisfied());
    }
    protected function getAppliedFunction($fn, $value, $arity) {
        $tmp = $fn;
        for ($i = 0; $i < $arity; ++$i) {
            $tmp = $tmp->apply($value);
        }
        return $tmp;
    }
}