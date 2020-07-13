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

class FloatTransformation implements Transformation
{
    const Reg_String = '/\s*(0|(-?[1-9]\d*([.,]\d+)?))\s*/';
    const Reg_String_Floating = '/\s*-?\d+[eE]-?\d+\s*/';

    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from)
    {
        if(true === is_int($from))
        {
            $from = (float)$from;
            return $from;
        }
        elseif(true === is_bool($from))
        {
            return floatval($from);
        }
        elseif(true === is_string($from))
        {
            if(preg_match(self::Reg_String, $from, $RegMatch))
            {
                $from = str_replace(',','.', $from);
                return floatval($from);

            }
            elseif(preg_match(self::Reg_String_Floating, $from, $RegMatch))
            {
                $from = floatval($from);
                return $from;
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

    /**
     * @inheritdoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}

