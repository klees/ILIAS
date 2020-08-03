<?php
/* Copyright (c) 2020 Luka K. A. Stocker, Extended GPL, see docs/LICENSE */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\ConstraintViolationException;

class IntegerTransformation implements Transformation
{
    const REG_INT = '/^\s*(0|(-?[1-9]\d*))\s*$/';

    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from)
    {
        if (is_float($from)) {
            $from = round($from);
            return intval($from);
        }

        if (is_bool($from)) {
            return (int) $from;
        }

        if (is_string($from) && $from <= PHP_INT_MAX || $from >= PHP_INT_MIN) {
            if (preg_match(self::REG_INT, $from)) {
                return intval($from);
            }
        }

        throw new ConstraintViolationException(
            sprintf('The value "%s" can not be transformed into an integer', $from),
            'not_integer',
            $from
        );
    }

    /**
     * @inheritdoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}
