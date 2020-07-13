<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

use ILIAS\Data\Result\Ok;
use ILIAS\Refinery\KindlyTo\Transformation\DictionaryTransformation;
use ILIAS\Refinery\KindlyTo\Transformation\StringTransformation;
use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Tests\Refinery\TestCase;

require_once ('./libs/composer/vendor/autoload.php');

class DictionaryTransformationTest extends TestCase
{
    const String_key = 'hello';
    const String_val = 'world';
    public function testDictionaryTransformation()
    {
        $transformation = new DictionaryTransformation(new StringTransformation());
        $transformedValue = $transformation->transform(array(self::String_key,self::String_val));
        $this->assertEquals(array(self::String_key,self::String_val), $transformedValue);
    }
}