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

const RegInt = '/\s*(0|(-?[1-9]\d*))\s*/';
const RegOctal = '/^0[0-7]+$/';

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
        elseif(true === is_string($from))
        {
            if(preg_match(RegInt, $from, $RegMatch))
            {
                $StrTrue = mb_strtolower("True");
                $StrFalse = mb_strtolower("False");
                $StrNull = mb_strtolower("Null");
                $NoVal = "";

                if(preg_match(RegOctal, $from, $RegMatch) || mb_strtolower($from) === ($StrTrue || $StrFalse || $StrNull || null || $NoVal))
                {
                    throw new ConstraintViolationException(
                        'The value can not be transformed into an integer',
                        'not_integer'
                    );
                }
                else
                {
                    $from = intval($from);
                    return $from;
                }
            }
        }
        elseif(true === is_bool($from))
        {
            $from = intval($from);
            return $from;
        }
        else
        {
            throw new ConstraintViolationException(
                'The value can not be transformed into an integer',
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

