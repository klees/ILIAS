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

    /**
     * @dataProvider TupleTransformationDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testTupleTransformation($originVal, $expectedVal)
    {
        $transformation = new TupleTransformation(
            array(new IntegerTransformation(), new IntegerTransformation())
        );

        $transformedValue = $transformation->transform($originVal);
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function TupleTransformationDataProvider()
    {
        return [
          'array_test01' => [array(1,2), [1,2]]
        ];
    }
}