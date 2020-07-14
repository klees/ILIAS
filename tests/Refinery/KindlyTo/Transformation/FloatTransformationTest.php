<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\FloatTransformation;
use ILIAS\Tests\Refinery\TestCase;

/**
 * Test transformations in this Group
 */
class FloatTransformationTest extends TestCase
{
    /**
     * @var FloatTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new FloatTransformation();
    }

    /**
     * @dataProvider FloatTestDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testFloatTransformation($originVal, $expectedVal)
    {
        $transformedValue = $this->transformation->transform($originVal);
        $this->assertIsFloat($transformedValue,'');
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function FloatTestDataProvider()
    {
        return [
            'pos_bool' => [true, 1.0],
            'neg_bool' => [false, 0.0],
            'string_comma' => ['234,23', 234.23],
            'string_floating_point' => ['7E10', 70000000000],
            'int_val' => [23, 23.0],
            'neg_int_val' => [-2, -2.0],
            'zero_int' => [0, 0.0]
        ];
    }
}