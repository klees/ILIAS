<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\DateTimeTransformation;
use PHPUnit\Framework\TestCase;

const DateExample = '2020-07-06 12:23:05';
const DateNew = '2020-07-06T12:23:06+0000';
const DateInt = '20200706';
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
        $original = new \DateTimeImmutable('2020-07-06 12:23:05');
        $expected = new \DateTimeImmutable('2020-07-06T12:23:06+0000');
        $transformedValue = $this->transformation->transform($original);

        $this->assertEquals($expected, $transformedValue);
    }

    public function testDateTimeToUnixTimestampTransformation()
    {
        $transformedValue = $this->transformation->transform(DateInt);

        $this->assertEquals(UnixDate, $transformedValue);

    }

}