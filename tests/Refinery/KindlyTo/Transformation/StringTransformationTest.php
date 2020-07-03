<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\To\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Data\Result;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Tests\Refinery\TestCase;

/**
 * Test transformations in this Group
 */
class StringTransformationTest extends TestCase
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
        $transformedValue = $this->transformation->transform('hello');

        $this->assertEquals('hello', $transformedValue);
    }

    public function testIntegerToStringTransformation()
    {
        $this->expectNotToPerformAssertions();

        try {
            $transformedValue = $this->transformation->transform(300);
        } catch (ConstraintViolationException $exception) {
            return;
        }

        $this->fail();
    }

    public function testNegativeIntegerToStringTransformation()
    {
        $this->expectNotToPerformAssertions();

        try        {
            $transformedValue = $this->transformation->transform(-300);
        } catch (ConstraintViolationException $exception) {
            return;
        }

        $this->assertEquals('-300', $transformedValue);
    }

    public function testZeroIntegerToStringTransformation()
    {
        $this->expectNotToPerformAssertions();

        try {
            $transformedValue = $this->transformation->transform(0);
        } catch (ConstraintViolationException $exception) {
            return;
        }

        $this->fail();
    }

    public function testPositiveBooleanToStringTransformation()
    {
        $this->expectNotToPerformAssertions();

        try {
            $transformedValue = $this->transformation->transform(true);
        } catch (ConstraintViolationException $exception) {
            return;
        }

        $this->fail();
    }

    public function testNegativeBooleanToStringTransformation()
    {
        $this->expectNotToPerformAssertions();

        try {
            $transformedValue = $this->transformation->transform(false);
        } catch (ConstraintViolationException $exception) {
            return;
        }

        $this->fail();
    }

    public function testFloatToStringTransformation()
    {
        $this->expectNotToPerformAssertions();

        try {
            $transformedValue = $this->transformation->transform(20.5);
        } catch (ConstraintViolationException $exception) {
            return;
        }

        $this->fail();
    }
}