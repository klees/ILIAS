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

class IntegerTransformation implements Transformation
{
    const Reg_Int = '/\s*(0|(-?[1-9]\d*))\s*/';
    const Reg_Octal = '/0[0-7]+/';

    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from)
    {

        if(true === is_float($from))
        {
            $from = round($from);
            $from = intval($from);
            return $from;
        }
        elseif(true === is_string($from) || $from <= PHP_INT_MAX || $from >= PHP_INT_MIN)
        {
            $StrTrue = mb_strtolower("True");
            $StrFalse = mb_strtolower("False");
            $StrNull = mb_strtolower("Null");
            $NoVal = "";
            $null = null;
            if(preg_match(self::Reg_Int, $from, $RegMatch))
            {
                if(preg_match(self::Reg_Octal, $from, $RegMatch))
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
            if(mb_strtolower($from) === ($StrTrue || $StrFalse || $StrNull || $null || $NoVal))
            {
                throw new ConstraintViolationException(
                'The value can not be transformed into an integer',
                'not_integer'
                );
            }
        }
        elseif(true === is_bool($from))
        {
            return (int)$from;
        }
        else
        {
            throw new ConstraintViolationException(
                'The value can not be transformed into an integer',
                'not_integer'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}

