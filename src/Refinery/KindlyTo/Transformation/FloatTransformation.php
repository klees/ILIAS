<?php
/* Copyright (c) 2020 Luka K. A. Stocker, Extended GPL, see docs/LICENSE */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\ConstraintViolationException;

class FloatTransformation implements Transformation {
    const REG_STRING = '/^\s*(0|(-?[1-9]\d*([.,]\d+)?))\s*$/';
    const REG_STRING_FLOATING = '/^\s*-?\d+[eE]-?\d+\s*$/';

    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from) {
        if(is_int($from)) {
            return (float)$from;
        }

        if(is_bool($from)) {
            return floatval($from);
        }

        if(is_string($from)) {

            $preg_match_string = preg_match(self::REG_STRING, $from, $RegMatch);
            $preg_match_floating_string = preg_match(self::REG_STRING_FLOATING, $from, $RegMatch);

            if($preg_match_string) {
                return floatval(str_replace(',','.', $from));
            }

            if($preg_match_floating_string) {
                return floatval($from);
            }

            if (!$preg_match_string && !$preg_match_floating_string){
                throw new ConstraintViolationException(
                    sprintf('The value "%s" could not be transformed into an float',$from),
                    'not_float',
                    $from
                );
            }
        }
        if (!is_string($from)) {
            throw new ConstraintViolationException(
                sprintf('The value "%s" is no string and could not be transformed into an float',$from),
                'not_float',
                $from
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function __invoke($from) {
        return $this->transform($from);
    }
}