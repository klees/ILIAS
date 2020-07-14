<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Refinery\KindlyTo\Transformation\IntegerTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\RecordTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;
use Symfony\Component\DependencyInjection\Tests\Compiler\I;

require_once('./libs/composer/vendor/autoload.php');

class RecordTransformationTest extends TestCase
{
    const string_key = 'stringKey';
    const int_key = 'integerKey';
    const second_int_key = 'integerKey2';

    /**
     * @dataProvider RecordTransformationDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testRecordTransformationIsValid($originVal, $expectedVal)
    {
        $recTransform = new RecordTransformation(
            array(
                self::string_key => new StringTransformation(),
                self::int_key => new IntegerTransformation()
            )
        );
        $transformedValue = $recTransform->transform($originVal);
        $this->assertIsArray($transformedValue,'');
        $this->assertEquals($expectedVal, $transformedValue);
    }

    /**
     * @dataProvider RecordFailureDataProvider
     * @param $origVal
     */
    public function testRecordTransformationFailures($origVal)
    {
        $this->expectNotToPerformAssertions();
        $recTransformation = new RecordTransformation(
            array(
                self::string_key => new StringTransformation(),
                self::int_key => new IntegerTransformation()
            )
        );

        try {
            $result = $recTransformation->transform($origVal);
        }catch (ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    public function testInvalidArray()
    {
        $this->expectNotToPerformAssertions();
        try {
            $recTransformation = new RecordTransformation(
                array(
                    new StringTransformation(),
                    new IntegerTransformation()
                )
            );
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    /**
     * @dataProvider RecordValueInvalidDataProvider
     * @param $originalValue
     */
    public function testInvalidValueDoesNotMatch($originalValue)
    {
        $this->expectNotToPerformAssertions();
        $recTransformation = new RecordTransformation(
            array(
                self::int_key => new IntegerTransformation(),
                self::second_int_key => new IntegerTransformation()
            )
        );

        try {
            $result = $recTransformation->transform()
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    public function RecordTransformationDataProvider()
    {
        return [
          [array('stringKey' => 'hello', 'integerKey' => 1), array('stringKey' => 'hello', 'integerKey' => 1)]
        ];
    }

    public function RecordFailureDataProvider()
    {
        return [
            'too_many_values' => [array('stringKey' => 'hello', 'integerKey' => 1, 'secondIntKey' => 1)],
            'key_is_not_a_string' => [array('testKey' => 'hello', 1)],
            'key_value_is_invalid' => [array('stringKey' => 'hello', 'integerKey2' => 1)]
        ];
    }

    public function RecordValueInvalidDataProvider()
    {
        return [
          'invalid_value' => [array('stringKey' => 'hello', 'integerKey2' => 1)]
        ];
    }
}
