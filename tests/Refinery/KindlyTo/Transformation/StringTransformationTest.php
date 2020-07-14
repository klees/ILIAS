<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;

/**
 * Test transformations in this Group
 */
class StringTransformationTest extends TestCase
{
    /**
     * @var StringTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new StringTransformation();
    }

    /**
     * @dataProvider StringTestDataProvider
     * @param $originVal
     * @param $expectedVal
     * @return string
     */
    public function testStringTransformation($originVal, $expectedVal)
    {
        $transformedValue = $this->transformation->transform($originVal);
        $this->assertInstanceOf(static::class,$transformedValue, '');
        $this->assertEquals($expectedVal, $transformedValue);

    }

    public function StringTestDataProvider()
    {
        return [
            'string_val' => ['hello', 'hello'],
            'int_val' => [300, '300'],
            'neg_int_val' => [-300, '-300'],
            'zero_int_val' => [0, '0'],
            'pos_bool' => [true, 'true'],
            'neg_bool' => [false, 'false'],
            'float_val' => [20.5, '20.5']
        ];
    }
}