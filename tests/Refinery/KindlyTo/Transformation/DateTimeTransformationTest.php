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
const UnixDate = '1593993600';

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
        $DateImmutable = new \DateTimeImmutable("2020-07-06T12:23:06+0000");
        $transformedValue = $this->transformation->transform(DateExample);

        $this->assertEquals($DateImmutable, $transformedValue);
    }

    public function testDateTimeToUnixTimestampTransformation()
    {
        $date = new \DateTime('DateExample');
        $transformedValue = $this->transformation->transform($date);

        $this->assertEquals(UnixDate, $transformedValue);

    }

}