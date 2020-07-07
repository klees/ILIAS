<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\DateTimeTransformation;
use PHPUnit\Framework\TestCase;

/**
 * Tests for datetime transformation
 */

class DateTimeTransformationTest extends TestCase
{
    /**
     * @var DateTimeTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new DateTimeTransformation();
    }

    public function testDateTimeTransformation()
    {
        $DateExample = '2020-07-06 12:23:05';
        $DateImmutable = new \DateTimeImmutable("2020-07-06 12:23:05");
        $DateIso8601 = $DateImmutable->format('Y-m-d\TH:i:sO');
        $transformedValue = $this->transformation->transform($DateExample);

        $this->assertEquals($DateIso8601, $transformedValue);
    }

    public function testDateTimeToUnixTimestampTransformation()
    {
        $DateExample = 2020-07-06;
        $UnixTimestamp = $DateExample->getTimestamp();

        $this->assertEquals($UnixTimestamp, $DateExample);
    }

}