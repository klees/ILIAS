<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Data\Result;
use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\ConstraintViolationException;

const RegString = '/\s*(0|(-?[1-9]\d*([.,]\d+)?))\s*/';
const RegStringFloating = '/\s*-?\d+[eE]-?\d+\s*/';

class FloatTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    public function transform($from)
    {
        if(true === is_int($from))
        {
            $from = (float)$from;
            return $from;
        }
        elseif(true === is_bool($from))
        {
            $from = floatval(str_replace(',','.', str_replace('.','', $from)));
            return $from;
        }
        elseif(true === is_string($from))
        {
            if(preg_match(RegString, $from, $RegMatch))
            {

            }
            elseif(preg_match(RegStringFloating, $from, $RegMatch))
            {

            }
            else
            {
                throw new ConstraintViolationException(
                    'The string could not be transformed into an float',
                    'not_float'
                );
            }
        }
        else
        {
            throw new ConstraintViolationException(
                'The value could not be transformed into an float',
                'not_float'
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

