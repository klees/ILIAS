<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\IntegerTransformation;
use ILIAS\Tests\Refinery\TestCase;


/**
 * Test transformations in this Group
 */
class IntegerTransformationTest extends TestCase
{
    /**
     * @var IntegerTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new IntegerTransformation();
    }

}