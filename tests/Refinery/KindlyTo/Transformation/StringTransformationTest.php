<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Data\Result;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;

const StringVal = 'hello';
const IntVal = 300;
const NegativeIntVal = -300;
const ZeroIntVal = 0;
const PositiveBool = true;
const NegativeBool = false;
const FloatVal = 20.5;
const StringTransformed = 'hello';
const IntTransformed = '300';
const NegativeIntTransformed = '-300';
const ZeroIntTransformed = '0';
const PositiveBoolTransformed = 'true';
const NegativeBoolTransformed = 'false';
const FloatValTransformed = '20.5';

/**
 * Test transformations in this Group
 */
abstract class KindlyToStringTransformationTest extends TestCase
{
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
        $transformedValue = $this->transformation->transform(StringVal);

        $this->assertEquals(StringTransformed, $transformedValue);
    }

    public function testIntegerToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(IntVal);

        $this->assertEquals(IntTransformed, $transformedValue);
    }

    public function testNegativeIntegerToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(NegativeIntVal);

        $this->assertEquals(NegativeIntTransformed, $transformedValue);
    }

    public function testZeroIntegerToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(ZeroIntVal);

        $this->assertEquals(ZeroIntTransformed, $transformedValue);
    }

    public function testPositiveBooleanToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(PositiveBool);

        $this->assertEquals(PositiveBoolTransformed, $transformedValue);
    }

    public function testNegativeBooleanToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(NegativeBool);

        $this->assertEquals(NegativeBoolTransformed, $transformedValue);
    }

    public function testFloatToStringTransformation()
    {
        $transformedValue = $this->transformation->transform(FloatVal);

        $this->assertEquals(FloatValTransformed, $transformedValue);
    }
}