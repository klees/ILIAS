<?php declare(strict_types=1);

/* Copyright (c) 2021 Luka Stocker <luka.stocker@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Tests\Refinery\Integer\Constraints;

use ILIAS\Data;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;
use ILIAS\Refinery\Integer\GreaterThanOrEqual;
use ilLanguage;

class GreaterThanOrEqualConstraintTest extends TestCase
{
    private Data\Factory $df;
    private ilLanguage $lng;

    public function setUp() : void
    {
        $this->df = new Data\Factory();
        $this->lng = $this->getMockBuilder(ilLanguage::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $greater_than_or_equal = 10;

        $this->c = new GreaterThanOrEqual(
            $greater_than_or_equal,
            $this->df,
            $this->lng
        );
    }

    public function testAccepts() : void
    {
        $this->assertTrue($this->c->accepts(12));
    }

    public function testNotAccepts() : void
    {
        $this->assertFalse($this->c->accepts(9));
    }

    public function testCheckSucceed() : void
    {
        $this->c->check(12);
        $this->assertTrue(true); // does not throw
    }

    public function testCheckFails() : void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->c->check(9);
    }

    public function testNoProblemWith() : void
    {
        $this->assertNull($this->c->problemWith(12));
    }

    public function testProblemWith() : void
    {
        $this->lng
            ->expects($this->once())
            ->method("txt")
            ->with("not_greater_than_or_equal")
            ->willReturn("-%s");

        $this->assertEquals("-10", $this->c->problemWith(9));
    }

    public function testRestrictOk() : void
    {
        $ok = $this->df->ok(12);

        $res = $this->c->applyTo($ok);
        $this->assertTrue($res->isOk());
    }

    public function testRestrictNotOk() : void
    {
        $not_ok = $this->df->ok(9);

        $res = $this->c->applyTo($not_ok);
        $this->assertFalse($res->isOk());
    }

    public function testRestrictError() : void
    {
        $error = $this->df->error("error");

        $res = $this->c->applyTo($error);
        $this->assertSame($error, $res);
    }

    public function testWithProblemBuilder() : void
    {
        $new_c = $this->c->withProblemBuilder(function () {
            return "This was a fault";
        });
        $this->assertEquals("This was a fault", $new_c->problemWith(9));
    }
}
