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
    const True_Bool = true;
    const Pos_Bool_Expected = 1;
    const Neg_Bool_Origin = false;
    const Neg_Bool_Expected = 0;
    const String_Origin_Float = '234,23';
    const String_Expected_Float = 234.23;
    const String_Float_Point_Origin = '7E10';
    const String_Float_Point_Expected = 70000000000;
    const Int_Origin = 23;
    const Int_Expected = 23.0;
    /**
     * @var FloatTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new FloatTransformation();
    }

    public function testPosBooleanToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(self::True_Bool);

        $this->assertEquals(self::Pos_Bool_Expected, $transformedValue);
    }

    public function testNegBooleanToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Neg_Bool_Expected);

        $this->assertEquals(self::Neg_Bool_Expected, $transformedValue);
    }
    public function testStringToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(self::String_Origin_Float);

        $this->assertEquals(self::String_Expected_Float, $transformedValue);
    }
    public function testStringFloatingPointToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(self::String_Float_Point_Origin);

        $this->assertEquals(self::String_Float_Point_Expected, $transformedValue);
    }
    public function testIntegerToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Int_Origin);

        $this->assertEquals(self::Int_Expected, $transformedValue);
    }
}