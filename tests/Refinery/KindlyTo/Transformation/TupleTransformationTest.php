<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocher@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\KindlyTo\Transformation\IntegerTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\TupleTransformation;
use ILIAS\Tests\Refinery\TestCase;

require_once ('./libs/composer/vendor/autoload.php');

class TupleTransformationTest extends TestCase
{
    const int_val_1 = 1;
    const int_val_2 = 2;
    public function testTupleTransformation()
    {
        $transformation = new TupleTransformation(
            array(new IntegerTransformation(), new IntegerTransformation())
        );

        $transformedValue = $transformation->transform(array(self::int_val_1,self::int_val_2));
        $this->assertEquals(array(self::int_val_1,self::int_val_2), $transformedValue);
    }
}