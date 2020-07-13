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
 * Tests for DateTimeImmutable and Unix Timetable transformation
 */

class DateTimeTransformationTest extends TestCase
{
    const Date_ISO = '2020-07-06T12:23:05+0000';
    const Date_Atom ='2020-07-06T12:23:05+00:00';
    const Date_RFC3339_EXT = '2020-07-06T12:23:05.000+00:00';
    const Date_Cookie = 'Monday, 06-Jul-2020 12:23:05 GMT+0000';
    const Date_RFC822 = 'Mon, 06 Jul 20 12:23:05 +0000';
    const Date_RFC7231 = 'Mon, 06 Jul 2020 12:23:05 GMT';
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

    /**
     * @dataProvider DateTimeTestDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testDateTimeISOTransformation($originVal, $expectedVal)
    {
        /**$expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ISO8601,self::Date_ISO);*/
        $transformedValue = $this->transformation->transform($originVal);
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function DateTimeTestDataProvider()
    {
        return [
            'ISO08601' => [\DateTime::createFromFormat(DATE_ISO8601,'2020-07-06T12:23:05+0000'),
                \DateTimeImmutable::createFromFormat(DATE_ISO8601,'2020-07-06T12:23:05+0000')],

        ];
    }
}