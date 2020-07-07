<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Data\Result\Ok;
use ILIAS\Refinery\KindlyTo\Transformation\DictionaryTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Tests\Refinery\TestCase;

require_once('./libs/composer/vendor/autoload.php');

const TestName = 'Max';
const TestAge = '49';

/**
 * Test transformation into dictionary (associative arrays)
 */

class DictionaryTransformationTest extends TestCase
{
    /**public function testDictionaryTransformation()
    {
        $transformation = new DictionaryTransformation(new StringTransformation());

        $result = $transformation->transform(array(TestName => TestAge));

        $this->assertEquals(array(TestName => TestAge), $result);
    }

     TBC ... */

}