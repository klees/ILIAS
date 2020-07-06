<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Tests\Refinery\KindlyTo\Transformation;

require_once('./libs/composer/vendor/autoload.php');

use ILIAS\Data\Result;
use ILIAS\Refinery\KindlyTo\Transformation\BooleanTransformation;
use ILIAS\Tests\Refinery\TestCase;

const PosBoolean = "true";
const NegBoolean = "false";
const PosBooleanNumber = 1;
const NegBooleanNumber = 0;
const PosBooleanNumberString = "1";
const NegBooleanNumberString = "0";
const TransformedPosBoolean = true;
const TransformedNegBoolean = false;

/**
* Test transformations in this Group
*/
class BooleanTransformationTest extends TestCase
{
    /**
     * @var BooleanTransformation
     */
    private $transformation;

    public function setUp(): void
    {
        $this->transformation = new BooleanTransformation();
    }

    public function testPosBooleanTransformation()
    {
        if(true === PosBoolean)
        {
            $transformedValue = $this->transformation->transform(PosBoolean);

            $this->assertEquals(TransformedPosBoolean, $transformedValue);
        }
        elseif(true === PosBooleanNumber)
        {
            $transformedValue = $this->transformation->transform(PosBooleanNumber);

            $this->assertEquals(TransformedPosBoolean, $transformedValue);
        }
        elseif(true === PosBooleanNumberString)
        {
            $transformedValue = $this->transformation->transform(PosBooleanNumberString);

            $this->assertEquals(TransformedPosBoolean, $transformedValue);
        }
    }

    public function testNegBooleanTransformation()
    {
        if(true === NegBoolean)
        {
            $transformedValue = $this->transformation->transform(NegBoolean);

            $this->assertEquals(TransformedNegBoolean, $transformedValue);
        }
        elseif(true === NegBooleanNumber)
        {
            $transformedValue = $this->transformation->transform(NegBooleanNumber);

            $this->assertEquals(TransformedNegBoolean, $transformedValue);
        }
        elseif(true === NegBooleanNumberString)
        {
            $transformedValue = $this->transformation->transform(NegBooleanNumberString);

            $this->assertEquals(TransformedNegBoolean, $transformedValue);
        }
    }
}