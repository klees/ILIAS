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

const RegInt = '/\s*(0|(-?[1-9]\d*))\s*/';

class IntegerTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    public function transform($from)
    {
        if(true === is_float($from))
        {
            $from = round($from);
            $from = intval($from);
            return $from;
        }
        if(true === is_string($from))
        {
            if(preg_match(RegInt, $from, $RegMatch))
            {
                $from = intval($from);
                return $from;
            }
        }
        if(true === is_bool($from))
        {
            $from = intval($from);
            return $from;
        }
        else
        {
            throw new ConstraintViolationException(
                'The value could not be transformed into an integer',
                'not_integer'
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

