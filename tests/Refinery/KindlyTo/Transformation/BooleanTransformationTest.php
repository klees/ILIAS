<?php
/* Copyright (c) 2020 Luka K. A. Stocker, Extended GPL, see docs/LICENSE */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\KindlyTo\Transformation\BooleanTransformation;
use ILIAS\Tests\Refinery\TestCase;
use ILIAS\Refinery\ConstraintViolationException;

/**
 * Test transformations in this Group
 */
class BooleanTransformationTest extends TestCase
{
    private $transformation;

    public function setUp() : void
    {
        $this->transformation = new BooleanTransformation();
    }

    /**
     * @dataProvider BooleanTestDataProvider
     * @param $originVal
     * @param bool $expectedVal
     */
    public function testBooleanTransformation($originVal, $expectedVal)
    {
        $transformedValue = $this->transformation->transform($originVal);
        $this->assertIsBool($transformedValue);
        $this->assertSame($expectedVal, $transformedValue);
    }

    public function BooleanTestDataProvider()
    {
        return [
            'pos_boolean' => ['true', true],
            'pos_boolean_number' => [1, true],
            'pos_boolean_number_string' => ['1', true],
            'neg_boolean' => ['false', false],
            'neg_boolean_number' => [0, false],
            'neg_boolean_number_string' => ['0', false]
        ];
    }
}
