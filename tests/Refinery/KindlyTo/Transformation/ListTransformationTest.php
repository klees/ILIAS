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

    public function testListTransformation()
    {
        $transformList = new ListTransformation(new StringTransformation());
        $transformedValue = $transformList->transform(array(self::first_arr, self::second_arr));

        $this->assertEquals(array(self::first_arr, self::second_arr), $transformedValue);
    }

    public function testNonArrayToArrayTransformation()
    {
        $transformList = new ListTransformation(new StringTransformation());
        $transformedValue = $transformList->transform(self::string_val);

        $this->assertEquals(array(self::string_val), $transformedValue);
    }

}