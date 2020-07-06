<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo;

use ILIAS\Refinery\KindlyTo\Group;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;

require_once('./libs/composer/vendor/autoload.php');

class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    private $basicGroup;

    public function setUp(): void
    {
        $this->basicGroup = new Group(new \ILIAS\Data\Factory());
    }

    public function testIsStringTransformationInstance()
    {
        $transformation = $this->basicGroup->string();

        $this->assertInstanceOf(StringTransformation::class, $transformation);
    }
}