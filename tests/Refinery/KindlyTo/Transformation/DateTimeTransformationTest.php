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

    public function testDateTimeISOTransformation()
    {
        /**$original = \DateTime::createFromFormat(DateTime::ISO8601,self::Date_ISO);*/
        $expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ISO8601,self::Date_ISO);
        $transformedValue = $this->transformation->transform(self::Date_ISO);

        $this->assertEquals($expected->format(\DateTimeImmutable::ISO8601), $transformedValue);
    }

    public function testDateTimeAtomTransformation()
    {
        /**$original = \DateTime::createFromFormat(DateTime::ATOM,self::Date_Atom);*/
        $expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM,self::Date_Atom);
        $transformedValue = $this->transformation->transform(self::Date_Atom);

        $this->assertEquals($expected->format(\DateTimeImmutable::ATOM), $transformedValue);
    }

    public function testDateTimeRFCExtTransformation()
    {
        $original = \DateTime::createFromFormat(DateTime::RFC3339_EXTENDED,self::Date_RFC3339_EXT);
        $original = $original->format(DateTime::RFC3339_EXTENDED);
        $expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC3339_EXTENDED,self::Date_RFC3339_EXT);
        $expected = $expected->format(\DateTimeImmutable::RFC3339_EXTENDED);
        $transformedValue = $this->transformation->transform($original);

        $this->assertEquals($expected, $transformedValue);
    }

    public function testDateTimeCookieTransformation()
    {
        $original = \DateTime::createFromFormat(DateTime::COOKIE,self::Date_Cookie);
        $original = $original->format(DateTime::COOKIE);
        $expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::COOKIE,self::Date_Cookie);
        $expected = $expected->format(\DateTimeImmutable::COOKIE);
        $transformedValue = $this->transformation->transform($original);

        $this->assertEquals($expected, $transformedValue);
    }

    public function testDateTimeRFC822Transformation()
    {
        $original = \DateTime::createFromFormat(DateTime::RFC822,self::Date_RFC822);
        $original = $original->format(DateTime::RFC822);
        $expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC822,self::Date_RFC822);
        $expected = $expected->format(\DateTimeImmutable::RFC822);
        $transformedValue = $this->transformation->transform($original);

        $this->assertEquals($expected, $transformedValue);
    }

    public function testDateTimeRFC7231Transformation()
    {
        $original = \DateTime::createFromFormat(DateTime::RFC7231,self::Date_RFC7231);
        $original = $original->format(DateTime::RFC7231);
        $expected = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC7231,self::Date_RFC7231);
        $expected = $expected->format(\DateTimeImmutable::RFC7231);
        $transformedValue = $this->transformation->transform($original);

        $this->assertEquals($expected, $transformedValue);
    }

    public function testDateTimeToUnixTimestampTransformation()
    {
        $transformedValue = $this->transformation->transform(self::Date_Int);

        $this->assertEquals(self::Unix_Date, $transformedValue);
    }
}