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

    public function testPosBooleanTransformation()
    {
            $transformedValue = $this->transformation->transform(self::Pos_Boolean);
            $this->assertEquals(self::Transformed_Pos_Boolean, $transformedValue);

            $transformedValue = $this->transformation->transform(self::Pos_Boolean_Number);
            $this->assertEquals(self::Transformed_Pos_Boolean, $transformedValue);

            $transformedValue = $this->transformation->transform(self::Pos_Boolean_Number_String);
            $this->assertEquals(self::Transformed_Pos_Boolean, $transformedValue);
    }

    public function testNegBooleanTransformation()
    {
            $transformedValue = $this->transformation->transform(self::Neg_Boolean);
            $this->assertEquals(self::Transformed_Neg_Boolean, $transformedValue);

            $transformedValue = $this->transformation->transform(self::Neg_Boolean_Number);
            $this->assertEquals(self::Transformed_Neg_Boolean, $transformedValue);

            $transformedValue = $this->transformation->transform(self::Neg_Boolean_Number_String);
            $this->assertEquals(self::Transformed_Neg_Boolean, $transformedValue);
    }
}