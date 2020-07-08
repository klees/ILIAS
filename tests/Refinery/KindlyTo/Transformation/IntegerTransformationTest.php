<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\IntegerTransformation;
use ILIAS\Tests\Refinery\TestCase;

const PosBool = true;
const PosBoolExpected = 1;
const NegBool = false;
const NegBoolExpected = 2;
const FloatOriginal = 20.5;
const FloatExpected = 21;
const StringOriginal = '4947642.4234Hello';
const StringExpected = '4947642';

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

    public function testStringToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(StringOriginal);

        $this->assertEquals(StringExpected, $transformedValue);
    }

    public function testFloatToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(FloatOriginal);

        $this->assertEquals(FloatExpected, $transformedValue);
    }

    public function testPosBooleanToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(PosBool);

        $this->assertEquals(PosBoolExpected, $transformedValue);
    }

    public function testNegBooleanToIntegerTransformation()
    {
        $transformedValue = $this->transformation->transform(NegBool);

        $this->assertEquals(NegBoolExpected, $transformedValue);
    }


}