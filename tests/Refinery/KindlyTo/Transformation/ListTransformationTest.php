<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Refinery\KindlyTo\Transformation\ListTransformation;
use ILIAS\Refinery\To\Transformation\StringTransformation;
use ILIAS\Tests\Refinery\TestCase;


/**
 * Test transformations in this Group
 */
class ListTransformationTest extends TestCase
{
    /**
     * @dataProvider ArrayToListTransformationDataProvider
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

     public function testInvalidListTransformation()
    {
        $this->expectNotToPerformAssertions();
        $transformList = new ListTransformation(new StringTransformation());
        try {
            $result = $transformList->transform(array('hello', 2));
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    /**
     * @dataProvider ArrayFailureDataProvider
     * @param $origValue
     */
    public function testOnNullAndEmptyArray($origValue)
    {
        $this->expectNotToPerformAssertions();
        $transformList = new ListTransformation(new StringTransformation());
        try{
            $result = $transformList->transform($origValue);
        }catch(ConstraintViolationException $exception)
        {
            return;
        }
        $this->fail();
    }

    public function ArrayToListTransformationDataProvider()
    {
        return [
            'first_arr' => [array('hello', 'world'), ['hello', 'world']],
            'second_arr' => [array('hello2','world2'), ['hello2', 'world2']],
            'string_val' => ['hello world',['hello world']]
        ];
    }

    public function ArrayFailureDataProvider()
    {
        return [
            'empty_array' => [array()],
            'null_array' => [array(null)]
        ];
    }


}