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

    const arr_String_Input = 'hello';
    const arr_Int_Input = 1;
    const string_key = 'stringKey';
    const int_key = 'integerKey';

    /**
     * @dataProvider RecordTransformationDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testRecordTransformation($originVal, $expectedVal)
    {
        $recTransform = new RecordTransformation(
            array(
                self::string_key => new StringTransformation(),
                self::int_key => new IntegerTransformation()
            )
        );
        $transformedValue = $recTransform->transform($originVal);
        $this->assertIsArray($transformedValue,'');
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function RecordTransformationDataProvider()
    {
        return [
          [array('stringKey' => 'hello', 'integerKey' => 1), array('stringKey' => 'hello', 'integerKey' => 1)]
        ];
    }
}
