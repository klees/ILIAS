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
    const String_Val = 'hello';
    const Int_Val = 300;
    const Negative_Int_Val = -300;
    const Zero_Int_Val = 0;
    const Positive_Bool = true;
    const Negative_Bool = false;
    const Float_Val = 20.5;
    const Int_Transformed = '300';
    const Negative_Int_Transformed = '-300';
    const Zero_Int_Transformed = '0';
    const Positive_Bool_Transformed = 'true';
    const Negative_Bool_Transformed = 'false';
    const Float_Val_Transformed = '20.5';

    /**
     * @var StringTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new StringTransformation();
    }

    public function testStringToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(self::String_Val);

        $this->assertEquals(self::String_Val, $transformedValue);
    }

    public function testIntegerToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Int_Val);

        $this->assertEquals(self::Int_Transformed, $transformedValue);
    }

    public function testNegativeIntegerToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Negative_Int_Val);

        $this->assertEquals(self::Negative_Int_Transformed, $transformedValue);
    }

    public function testZeroIntegerToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Zero_Int_Val);

        $this->assertEquals(self::Zero_Int_Transformed, $transformedValue);
    }

    public function testPositiveBooleanToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Positive_Bool);

        $this->assertEquals(self::Positive_Bool_Transformed, $transformedValue);
    }

    public function testNegativeBooleanToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Negative_Bool);

        $this->assertEquals(self::Negative_Bool_Transformed, $transformedValue);
    }

    public function testFloatToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Float_Val);

        $this->assertEquals(self::Float_Val_Transformed, $transformedValue);
    }
}