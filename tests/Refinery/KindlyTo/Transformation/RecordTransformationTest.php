<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\KindlyTo\Transformation\IntegerTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\RecordTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;

require_once('./libs/composer/vendor/autoload.php');

class RecordTransformationTest extends TestCase
{
    /**
     * @dataProvider RecordTransformationDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    const string_key = 'hello';
    const int_key = 1;
    public function testRecordTransformation($originVal, $expectedVal)
    {
        $recTransform = new RecordTransformation(
            array(
                self::string_key => new StringTransformation(),
                self::int_key => new IntegerTransformation()
            )
        );
        $transformedValue = $recTransform->transform($originVal);
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function RecordTransformationDataProvider()
    {
        return [
          'first_arr_rec' => [array('intKey' => 1, 'stringKey' => 'hello'), ['intKey' => 1, 'stringKey' => 'hello']]
        ];
    }
}
