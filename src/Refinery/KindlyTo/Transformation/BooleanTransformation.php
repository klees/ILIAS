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

class BooleanTransformation implements Transformation
{
    const Bool_True_String = 'true';
    const Bool_False_String = 'false';
    const Bool_True_Number = 1;
    const Bool_False_Number = 0;
    const Bool_True_Number_String = '1';
    const Bool_False_Number_String = '0';

    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from)
    {
        if($from === self::Bool_True_Number || $from === self::Bool_True_Number_String || mb_strtolower($from) === self::Bool_True_String)
        {
            $from = boolval(true);
            return $from;
        }
        elseif($from === self::Bool_False_Number || $from === self::Bool_False_Number_String || mb_strtolower($from) === self::Bool_False_String)
        {
            $from = boolval(false);
            return $from;
        }
        else {
            throw new ConstraintViolationException(
                'The value could not be transformed into boolean.',
                'not_boolean'
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
