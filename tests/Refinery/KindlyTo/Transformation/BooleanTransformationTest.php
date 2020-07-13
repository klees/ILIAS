<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\BooleanTransformation;
use ILIAS\Tests\Refinery\TestCase;

/**
* Test transformations in this Group
*/
class BooleanTransformationTest extends TestCase
{
    const Pos_Boolean = 'true';
    const Neg_Boolean = 'false';
    const Pos_Boolean_Number = 1;
    const Neg_Boolean_Number = 0;
    const Pos_Boolean_Number_String = '1';
    const Neg_Boolean_Number_String = '0';
    const Transformed_Pos_Boolean = true;
    const Transformed_Neg_Boolean = false;

    /**
     * @var BooleanTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new BooleanTransformation();
    }

    /**
     * @dataProvider PosBooleanTestDataProvider
     * @dataProvider NegBooleanTestDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testBooleanTransformation($originVal, $expectedVal)
    {
            $transformedValue = $this->transformation->transform($originVal);
            $this->assertSame($expectedVal, $transformedValue);
    }

    public function PosBooleanTestDataProvider()
    {
        return [
            'pos_boolean' => ['true', true],
            'pos_boolean_number' => [1, true],
            'pos_boolean_number_string' => ['1', true]
        ];
    }

    public function NegBooleanTestDataProvider()
    {
        return [
            'neg_boolean' => ['false', false],
            'neg_boolean_number' => [0, false],
            'neg_boolean_number_string' => ['0', false]
        ];
    }
}