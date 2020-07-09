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

class StringTransformation implements Transformation
{
    const Bool_True = true;
    const Bool_False = false;
    const Bool_True_Number = 1;
    const Bool_False_Number = 0;
    const Bool_True_String = 'true';
    const Bool_False_String = 'false';

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
        elseif (true === is_bool($from) || $from === self::Bool_True_Number || $from === self::Bool_False_Number)
        {
            if($from === self::Bool_True || $from === self::Bool_True_Number)
            {
                $from = strval(self::Bool_True_String);
                return $from;
            }
            elseif($from === self::Bool_False || $from === self::Bool_False_Number)
            {
                $from = strval(self::Bool_False_String);
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

