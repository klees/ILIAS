<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\BooleanTransformation;
use ILIAS\Tests\Refinery\TestCase;
use Sabre\VObject\Property\Boolean;


/**
* Test transformations in this Group
*/

class BooleanTransformationTest extends TestCase
{
    /**
     * @var BooleanTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new BooleanTransformation();
    }

     /**
     * @dataProvider BooleanTestDataProvider
     * @param $originVal
     * @param bool $expectedVal
     */
    public function testBooleanTransformation($originVal, bool $expectedVal)
    {
            $transformedValue = $this->transformation->transform($originVal);
            $this->assertIsBool($transformedValue, '');
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