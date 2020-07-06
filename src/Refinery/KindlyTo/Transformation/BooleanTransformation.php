<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Data\Result;
use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\ConstraintViolationException;

const BoolTrueString = 'true';
const BoolFalseString = 'false';
const BoolTrueNumber = 1;
const BoolFalseNumber = 0;
const BoolTrueNumberString = '1';
const BoolFalseNumberString = '0';

class BooleanTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    public function transform($from)
    {
        if($from === BoolTrueNumber || $from === BoolTrueNumberString || $from === BoolTrueString)
        {
            $from = true;
            return $from;
        }
        elseif($from === BoolFalseNumber || $from === BoolFalseNumberString || $from === BoolFalseString)
        {
            $from = false;
            return $from;
        }
        else {
            throw new ConstraintViolationException(
                'The value could not be transformed into boolean.',
                'not_boolean'
            );
        }

    }
    public function applyTo(Result $data): Result
    {
    }

    public function __invoke($from)
    {
        return $this->transform($from);
    }
}
