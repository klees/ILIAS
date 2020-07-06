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
    public function transform($from)
    {
        /** if (false === is_string($from)) {
            throw new ConstraintViolationException(
                'The value MUST be of type string',
                'not_string'
            );
        }*/
        return (string) $from;
    }

    public function applyTo(Result $data): Result
    {
        // TODO: Implement applyTo() method.
    }

    public function __invoke($from)
    {
        return $this->transform($from);
    }
}

