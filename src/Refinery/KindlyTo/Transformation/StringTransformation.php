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

const BoolTrue = true;
const BoolFalse = false;
const BoolTrueNumber = 1;
const BoolFalseNumber = 0;
const BoolTrueString = 'true';
const BoolFalseString = 'false';

class StringTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    public function transform($from)
    {
        if(true === is_int($from) || true === is_float($from) || true === is_double($from))
        {
            $from = strval($from);
            return $from;
        }
        elseif (true === is_string($from))
        {
            return $from;
        }
        elseif (true === is_bool($from) || $from === BoolTrueNumber || $from === BoolFalseNumber)
        {
            if($from === BoolTrue || $from === BoolTrueNumber)
            {
                $from = strval(BoolTrueString);
                return $from;
            }
            elseif($from === BoolFalse || $from === BoolFalseNumber)
            {
                $from = strval(BoolFalseString);
                return $from;
            }
        }
        elseif (false === is_string($from))
        {
            throw new ConstraintViolationException(
                'The value could not be transformed into a string',
        'not_string'
                );
        }
    }

    public $from;
    public function __toString()
    {
        return "{$this->from}";
    }

    public function applyTo(Result $data): Result
    {
    }

    public function __invoke($from)
    {
        return $this->transform($from);
    }
}

