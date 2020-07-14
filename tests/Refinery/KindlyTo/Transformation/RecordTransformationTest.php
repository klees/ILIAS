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

    public function testRecordTransformation()
    {
        $recTransform = new RecordTransformation(
            array(
                self::string_key => new StringTransformation(),
                self::int_key => new IntegerTransformation()
            )
        );
        $transformedValue = $recTransform->transform(array(self::string_key => self::arr_String_Input, self::int_key => self::arr_Int_Input));
        $this->assertEquals(array(self::string_key => self::arr_String_Input, self::int_key => self::arr_Int_Input), $transformedValue);
    }
}
