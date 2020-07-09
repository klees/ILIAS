<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\FloatTransformation;
use ILIAS\Tests\Refinery\TestCase;

const PosBool = true;
const PosBoolExpected = 1.0;
const NegBoolOrigin = false;
const NegBoolExpected = 0.0;
const StringOriginFloat = '234,23'; /** was ist mit z.B. 2.434,32 oder 2,434.32 ??? */
const StringExpectedFloat = 234.23; /** komma zu punkt oder ohne punkt??? => 234 anstatt 234.23 */
const StringFloatPointOrigin = '7E10';
const StringFloatPointExpected = 70000000000;
const IntOrigin = 23;
const IntExpected = 23.0;


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
        $transformation = new FloatTransformation();
    }

    public function testPosBooleanToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(PosBool);

        $this->assertEquals(PosBoolExpected, $transformedValue);
    }

    public function testNegBooleanToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(NegBoolOrigin);

        $this->assertEquals(NegBoolExpected, $transformedValue);
    }
    public function testStringToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(StringOriginFloat);

        $this->assertEquals(StringExpectedFloat, $transformedValue);
    }
    public function testStringFloatingPointToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(StringFloatPointOrigin);

        $this->assertEquals(StringFloatPointExpected, $transformedValue);
    }
    public function testIntegerToFloatTransformation()
    {
        $transformedValue = $this->transformation->transform(IntOrigin);

        $this->assertEquals(IntExpected, $transformedValue);
    }
}