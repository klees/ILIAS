<?php
declare(strict_types=1);
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\ConstraintViolationException;

class RecordTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    /**
     *@var Transformation[]
     */
    private $transformations;

    /**
     *@param Transformation[] $transformations
     */

    public function __construct(array $transformations)
    {
        foreach($transformations as $key => $transformation)
        {
            if(false === is_string($key))
            {
                throw new ConstraintViolationException(
                    'The array key must be a string',
                    'key_is_not_a_string'
                );
            }
        }
        $this->transformations = $transformations;
    }

    /**
     * @inheritDoc
     */
    public function transform($from)
    {
        if(false == is_array($from))
        {
            $from = array($from);
            if(array() === $from)
            {
                throw new ConstraintViolationException(
                    'The array ist empty',
                    'value_array_is_empty'
                ) ;
            }
        }
        elseif(array() === $from)
        {
            throw new ConstraintViolationException(
                'The array ist empty',
                'value_array_is_empty'
            ) ;
        }

        $result = array();
        foreach($from as $key => $value)
        {
            if (false === is_string($key))
            {
                throw new ConstraintViolationException(
                    'Array key must be a string',
                    'key_is_not_a_string'
                );
            }
            $transformation = $this->transformations[$key];
            if(false === isset($transformation))
            {
                throw new ConstraintViolationException(
                    'Could not find transformation',
                    'array_key_does_not_exist'
                );
            }

            $transformedValue = $transformation->transform($value);
            $result[$key] = $transformedValue;
        }
        return $result;

    }

    /**
     * @param $values
     * @throws ConstraintViolationException
     */
    private function ValueLength($values)
    {
        $countOfValues = count($values);
        $countOfTransformations = count($this->transformations);

        if ($countOfValues !== $countOfTransformations) {
            throw new ConstraintViolationException(

                    'The given values does not match',
                    'value_length_does_not_match'

            );
        }
    }

    /**
     * @inheritDoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}