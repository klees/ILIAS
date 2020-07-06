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
        elseif (true === is_bool($from))
        {
            $from = (boolval($from) ? 'true' : 'false');
            $from = strval($from);
            return $from;
        }
        else
        {
            if (false === is_string($from)) {
                throw new ConstraintViolationException(
                    'The value could not be transformed into a string',
                    'not_string'
                );
            }
        }
    }

    public function __toString($form)
    {
        return (string) $form;
    }

    public function applyTo(Result $data): Result
    {
    }

    public function __invoke($from)
    {
        return $this->transform($from);
    }
}

