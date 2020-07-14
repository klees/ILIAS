<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\KindlyTo\Transformation\ListTransformation;
use ILIAS\Refinery\To\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;


/**
 * Test transformations in this Group
 */
class ListTransformationTest extends TestCase
{
    const first_arr = 'hello';
    const second_arr = 'world';
    const string_val = 'hello world';

    /**
     * @dataProvider ArrayToListTransformation
     * @param $originValue
     * @param $expectedValue
     */
    public function testListTransformation($originValue, $expectedValue)
    {
        $transformList = new ListTransformation(new StringTransformation());
        $transformedValue = $transformList->transform($originValue);
        $this->assertIsArray($transformedValue,'');
        $this->assertEquals($expectedValue, $transformedValue);
    }

    /**
     * @dataProvider StringToListTransformationDataProvider
     * @param $originVal
     * @param $expectedVal
     */
    public function testNonArrayToArrayTransformation($originVal,$expectedVal)
    {
        $transformList = new ListTransformation(new StringTransformation());
        $transformedValue = $transformList->transform($originVal);
        $this->assertIsArray($transformedValue,'');
        $this->assertEquals($expectedVal, $transformedValue);
    }

    public function StringToListTransformationDataProvider()
    {
        return [
            'string_val' => ['hello world',['hello world']],
        ];
    }

    public function ArrayToListTransformation()
    {
        return [
            'first_arr' => [array('hello', 'world'), ['hello', 'world']],
            'second_arr' => [array('hello2','world2'), ['hello2', 'world2']]
        ];
    }
}