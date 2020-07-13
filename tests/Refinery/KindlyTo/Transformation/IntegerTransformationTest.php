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
    const Pos_Bool = true;
    const Pos_Bool_Expected = 1;
    const Neg_Bool = false;
    const Neg_Bool_Expected = 0;
    const Float_Original = 20.5;
    const Float_Expected = 21;
    const String_Original = '4947642.4234Hello';
    const String_Expected = '4947642';

    /**
     * @var IntegerTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new IntegerTransformation();
    }

    public function testStringToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(self::String_Original);
        $this->assertEquals(self::String_Expected, $transformedValue);
    }

    public function testFloatToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Float_Original);
        $this->assertEquals(self::Float_Expected, $transformedValue);
    }

    public function testPosBooleanToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Pos_Bool);
        $this->assertEquals(self::Pos_Bool_Expected, $transformedValue);
    }

    public function testNegBooleanToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Neg_Bool);
        $this->assertEquals(self::Neg_Bool_Expected, $transformedValue);
    }
}