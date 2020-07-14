<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\KindlyTo\Transformation\DictionaryTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Tests\Refinery\TestCase;

require_once ('./libs/composer/vendor/autoload.php');

class DictionaryTransformationTest extends TestCase
{
    const String_key = 'hello';
    const String_val = 'world';

    /**
     * @dataProvider DictionaryTransformationDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testDictionaryTransformation($originVal, $expectedVal)
    {
        $transformation = new DictionaryTransformation(new StringTransformation());
        $transformedValue = $transformation->transform($originVal);
        $this->assertIsArray($transformedValue);
        $this->assertEquals($expectedVal, $transformedValue);
    }

    /**
     * @dataProvider TransformationFailingDataProvider
     * @param $failingVal
     */
    public function testTransformationFailures($failingVal)
    {
        $this->expectNotToPerformAssertions();
        $transformation = new DictionaryTransformation(new StringTransformation());
        try {
            $result = $transformation->transform($failingVal);
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    public function TransformationFailingDataProvider()
    {
        return [
            'key_not_a_string' => ['hello'],
            'value_not_a_string' => ['hello' => 1],
            'empty_array' => [array()]
        ];
    }

    public function DictionaryTransformationDataProvider()
    {
        return [
            'first_arr' => [array('hello' => 'world'), ['hello' => 'world'] ],
            'second_arr' => [array('hi' => 'earth', 'goodbye' => 'world'),['hi' => 'earth', 'goodbye' => 'world']]
        ];
    }
}