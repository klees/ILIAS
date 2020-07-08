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

const DateOrigin = '2020-07-06T12:23:05+0000';
const ISO8601 = 'Y-m-d\TH:i:sO';
const DateInt = 20200706122305;
const UnixDate = '1594038185';

/**
 * Tests for DateTimeImmutable and Unix Timetable transformation
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
        $original = DateTime::createFromFormat(ISO8601,DateOrigin);
        $expected = new \DateTimeImmutable(DateOrigin);
        //$expected = \DateTimeImmutable::createFromFormat(ISO8601, DateOrigin);
        $transformedValue = $this->transformation->transform($original);

        $this->assertEquals($expected, $transformedValue);
    }

    public function testDateTimeToUnixTimestampTransformation()
    {
        $transformedValue = $this->transformation->transform(DateInt);

        $this->assertEquals(UnixDate, $transformedValue);

    }

}