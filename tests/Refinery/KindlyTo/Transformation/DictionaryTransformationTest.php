<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\KindlyTo\Transformation\DictionaryTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;

require_once ('./libs/composer/vendor/autoload.php');

class DictionaryTransformationTest extends TestCase
{
    /**
     * @dataProvider DictionaryTestDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testDictionaryTransformation($originVal, $expectedVal)
    {
        $transformation = new DictionaryTransformation(new StringTransformation());
        $transformedValue = $transformation->transform($originVal);
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function DictionaryTestDataProvider()
    {
        return array(
            array('hello' => 'world'),
            array('hello2' => array('world2', 'world3', 'world4'))
        );
    }
}