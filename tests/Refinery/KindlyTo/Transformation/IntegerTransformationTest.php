<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\IntegerTransformation;
use ILIAS\Tests\Refinery\TestCase;

/**
 * Test transformations in this Group
 */
class IntegerTransformationTest extends TestCase
{
    /**
     * @var IntegerTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new IntegerTransformation();
    }

    /**
     * @dataProvider IntegerTestDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testIntegerTransformation($originVal, $expectedVal)
    {
        $transformedValue = $this->transformation->transform($originVal);
        $this->assertIsInt($transformedValue);
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function IntegerTestDataProvider()
    {
        return [
            'pos_bool' => [true, (int)1],
            'neg_bool' => [false, (int)0],
            'float_val' => [20.5, 21],
            'string_val' => ['4947642.4234Hello', '4947642']
        ];
    }

}