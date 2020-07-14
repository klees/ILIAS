<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocher@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Refinery\KindlyTo\Transformation\IntegerTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\TupleTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;

require_once ('./libs/composer/vendor/autoload.php');

class TupleTransformationTest extends TestCase
{
    const tuple_key = 'hello';
    /**
     * @dataProvider TupleTransformationDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testTupleTransformation($originVal, $expectedVal)
    {
        $transformation = new TupleTransformation(
            array(new IntegerTransformation(), new IntegerTransformation())
        );

        $transformedValue = $transformation->transform($originVal);
        $this->assertIsArray($transformedValue);
        $this->assertEquals($expectedVal, $transformedValue);
    }

    /**
     * @dataProvider TupleFailingTransformationDataProvider
     * @param $failingVal
     */
    public function testTupleFailingTransformations($failingVal)
    {
        $this->expectNotToPerformAssertions();
        $transformation = new TupleTransformation(
            array(
                new IntegerTransformation(),
                new StringTransformation()
            )
        );

        try {
            $result = $transformation->transform($failingVal);
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    /**
     * @dataProvider TupleFailingTransformationDataProvider
     * @param $failingVal
     */
    public function testNewTupleIsIncorrect($failingVal)
    {
        $this->expectNotToPerformAssertions();
        $transformation = new TupleTransformation(
            array(
                new IntegerTransformation(),
                self::tuple_key => new IntegerTransformation()
            )
        );

        try {
            $result = $transformation->transform($failingVal);
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    /**
     * @dataProvider TupleTooManyValuesDataProvider
     * @param $tooManyValues
     */
    public function testTupleTooManyValues($tooManyValues)
    {
        $this->expectNotToPerformAssertions();
        $transformation = new TupleTransformation(
            array(
                new IntegerTransformation(),
                new IntegerTransformation()
            )
        );

        try {
            $result = $transformation->transform($tooManyValues);
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    public function TupleTooManyValuesDataProvider()
    {
        return [
            'too_many_values' => [array(1,2,3)]
        ];
    }

    public function TupleFailingTransformationDataProvider()
    {
        return [
            'incorrect_tuple' => [array(1,2)]
        ];
    }

    public function TupleTransformationDataProvider()
    {
        return [
          'array_test01' => [array(1,2), [1,2]]
        ];
    }
}