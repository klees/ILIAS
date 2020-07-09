<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use DateTime;
use ILIAS\Refinery\KindlyTo\Transformation\DateTimeTransformation;
use PHPUnit\Framework\TestCase;

/**
 * Tests for DateTimeImmutable and Unix Timetable transformation
 */

class DateTimeTransformationTest extends TestCase
{
    const Date_Origin = '2020-07-06T12:23:05+0000';
    const Date_Int = 20200706122305;
    const Unix_Date = '1594038185';

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
        $original = DateTime::createFromFormat(DateTime::ISO8601,self::Date_Origin);
        $expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ISO8601,self::Date_Origin);

        $transformedValue = $this->transformation->transform($original);

        $this->assertEquals($expected, $transformedValue);
    }

    public function testDateTimeToUnixTimestampTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Date_Int);

        $this->assertEquals(self::Unix_Date, $transformedValue);

    }

}